<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\LineService;
use Illuminate\Support\Facades\Log;

class SendLineMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 10, 20]; // seconds
    
    protected $type;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
        
        // Set delay to avoid rate limiting
        $this->delay(now()->addSeconds(2));
    }

    /**
     * Execute the job.
     */
    public function handle(LineService $lineService): void
    {
        try {
            $result = false;
            
            switch ($this->type) {
                case 'flex':
                    $result = $lineService->sendGroupFlexMessage(
                        $this->data['altText'],
                        $this->data['flexContents']
                    );
                    break;
                    
                case 'text':
                    $result = $lineService->sendGroupTextMessage($this->data['text']);
                    break;

                case 'flex2':
                    $result = $lineService->sendGroupFlexMessage2(
                        $this->data['altText'],
                        $this->data['flexContents']
                    );
                    break;

                case 'text2':
                    $result = $lineService->sendGroupTextMessage2($this->data['text']);
                    break;
                    
                default:
                    Log::error('Unknown LINE message type: ' . $this->type);
                    return;
            }

            if (!$result) {
                Log::error('Failed to send LINE message, attempting email fallback', [
                    'type' => $this->type,
                    'data' => $this->data
                ]);
                
                // Send email as fallback
                $this->sendEmailFallback();
            }
        } catch (\Exception $e) {
            Log::error('LINE message job failed: ' . $e->getMessage(), [
                'type' => $this->type,
                'data' => $this->data
            ]);
            
            // Check if it's a LINE API issue
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'Too Many Requests')) {
                Log::warning('LINE API rate limited, retrying later...');
                $this->release(60); // Wait 1 minute before retry
                return;
            }
            
            // For other errors, send email fallback
            Log::info('LINE API unavailable, sending email fallback');
            $this->sendEmailFallback();
        }
    }
    
    /**
     * Send email as fallback when LINE fails
     */
    protected function sendEmailFallback()
    {
        try {
            $recipients = env('LINE_FALLBACK_EMAIL_RECIPIENTS', 'admin@example.com');
            $emails = array_map('trim', explode(',', $recipients));
            
            $subject = '[LINE FALLBACK] ';
            $content = '';
            
            if ($this->type === 'flex') {
                $subject .= $this->data['altText'];
                $content = 'LINE Flex Message Content: ' . json_encode($this->data['flexContents'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } elseif ($this->type === 'text') {
                $subject .= 'LINE Text Message';
                $content = $this->data['text'];
            }
            
            // Simple mail send (you can enhance this with proper Mail class)
            foreach ($emails as $email) {
                \Mail::raw($content, function ($message) use ($email, $subject) {
                    $message->to($email)
                           ->subject($subject);
                });
            }
            
            Log::info('Email fallback sent successfully', [
                'recipients' => $emails,
                'type' => $this->type
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email fallback: ' . $e->getMessage());
        }
    }
}
