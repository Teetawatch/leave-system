# Design Guidelines

## Input Fields (TextField / TextFormField)

When implementing input fields, follow these rules to ensure visual consistency and avoid "double border" or stack issues:

1.  **Do NOT wrap inputs in decorated Containers**: Avoid wrapping `TextField` or `TextFormField` in a `Container` that has `decoration: BoxDecoration(...)` with borders or background colors.
    *   *Why*: This often causes visual glitches where the container's border conflicts with the input's own internal border, or creates double borders.

2.  **Use `InputDecoration` for styling**:
    *   Set `filled: true` and `fillColor: ...` inside `InputDecoration` if a background is needed.
    *   Set `contentPadding` directly in `InputDecoration`.
    *   Use `prefixIcon` and `suffixIcon` instead of external `Row`s if possible.

3.  **Leverage `AppTheme`**:
    *   The `DropdownButtonFormField` and `TextFormField` should automatically pick up the global `inputDecorationTheme` from `AppTheme.dart`.
    *   If you need to override (e.g., for a "borderless" look inside a custom card), explicitly set `border: InputBorder.none` in the `InputDecoration`.

### Example (Correct)

```dart
TextFormField(
  decoration: InputDecoration(
    hintText: 'Search...',
    prefixIcon: Icon(Icons.search),
    // Standard theme handles border and fill
  ),
)
```

### Example (Incorrect - Do Not Use)

```dart
Container(
  decoration: BoxDecoration(
    border: Border.all(color: Colors.grey), // Don't do this
    borderRadius: BorderRadius.circular(12),
  ),
  child: TextFormField(
    decoration: InputDecoration(
      border: InputBorder.none, // Trying to hide inner border
    ),
  ),
)
```
