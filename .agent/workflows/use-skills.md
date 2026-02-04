---
description: วิธีการใช้ Skills อัตโนมัติในการตอบคำถามทุกครั้ง
---

# การใช้ Skills อัตโนมัติ

## 📌 หลักการสำคัญ

ทุกครั้งที่ได้รับคำขอจากผู้ใช้ ให้ปฏิบัติตามขั้นตอนนี้:

## ขั้นตอนที่ 1: วิเคราะห์คำขอ

พิจารณาว่าคำขอเกี่ยวข้องกับหมวดหมู่ใด:

| หมวดหมู่ | คีย์เวิร์ด |
|----------|-----------|
| Architecture | ออกแบบระบบ, system design, ADR, C4, scalable |
| Business | SEO, marketing, pricing, growth, copywriting |
| Data & AI | LLM, RAG, AI, prompt, agent, analytics |
| Development | React, TypeScript, Python, Laravel, API |
| Security | security, penetration, vulnerability, auth |
| Testing | test, TDD, unit test, integration test |
| Infrastructure | Docker, AWS, deployment, CI/CD, serverless |

## ขั้นตอนที่ 2: ค้นหา Skill ที่เกี่ยวข้อง

// turbo
1. ค้นหาใน `skills_index.json` ที่ `.agent/skills/skills_index.json`
2. หรือค้นหาในโฟลเดอร์ `skills/` ด้วยชื่อที่ตรงกับงาน

## ขั้นตอนที่ 3: อ่าน SKILL.md

เมื่อพบ skill ที่เกี่ยวข้อง:

// turbo
1. ใช้ `view_file` อ่านไฟล์ `SKILL.md` ในโฟลเดอร์ skill นั้น
2. เช่น: `.agent/skills/skills/react-patterns/SKILL.md`

## ขั้นตอนที่ 4: ทำงานตาม Skill

ปฏิบัติตามคำแนะนำและ best practices ที่ระบุใน SKILL.md

---

## 🔍 ตัวอย่างการ Mapping

| คำขอผู้ใช้ | Skill ที่ใช้ |
|-----------|-------------|
| วางแผนฟีเจอร์ | `brainstorming` |
| สร้าง React component | `react-patterns` |
| ตรวจสอบ API security | `api-security-best-practices` |
| ปรับปรุง performance | `performance-optimization` |
| เขียน unit test | `test-driven-development` |
| สร้าง Docker | `docker-expert` |
| ออกแบบ database | `database-optimization` |
| สร้าง AI agent | `ai-agents-architect` |

---

## 📂 ตำแหน่ง Skills

- **Index**: `.agent/skills/skills_index.json`
- **Skills Folder**: `.agent/skills/skills/`
- **จำนวน Skills**: 2,550+ skills

## ⚡ หมายเหตุสำคัญ

1. **ไม่ต้องรอให้ผู้ใช้บอกให้ใช้ skill** - ค้นหาและใช้อัตโนมัติ
2. **อ่าน SKILL.md ก่อนทำงาน** - เพื่อเข้าใจแนวทางที่ถูกต้อง
3. **ใช้หลาย skills ได้** - ถ้างานต้องการความเชี่ยวชาญหลายด้าน
