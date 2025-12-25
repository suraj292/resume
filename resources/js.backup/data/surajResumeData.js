import { parseComprehensiveResume } from './utils/resumeParser.js'

const resumeText = `SURAJ SHARMA
Senior Full-Stack Developer (Laravel  React/Next.js  Vue.js)
Sahibabad, Ghaziabad  +91 7042611736  surajkumarsharma123@gmail.com
LinkedIn: linkedin.com/in/surajsharma1999  GitHub: github.com/suraj292
PROFILE SUMMARY
Senior Full-Stack Developer with 4+ years of experience building scalable, high-performance
applications using Laravel, Vue.js, React/Next.js, MySQL, Redis, and DevOps workflows. Expert in
API architecture, microservices, queues, caching, CI/CD, and cloud deployments.
SKILLS
Backend: Laravel, PHP OOP, REST APIs, Microservices, MySQL, PostgreSQL, MongoDB, Redis,
Horizon
Frontend: React.js, Next.js, Vue.js 3, TailwindCSS, Redux, Zustand
DevOps: GitHub Actions, Docker, Nginx, DigitalOcean, AWS
Other: SOLID, Clean Architecture, Payment Integrations, Third-Party APIs
PROFESSIONAL EXPERIENCE
Full-Stack Developer — Webvio Technologies (Oct 2025 – Present, Delhi NCR)
 Leading Laravel + React/Next.js projects with clean architecture.
 Designing scalable REST APIs, Redis caching, Horizon queues.
 Managing CI/CD pipelines and cloud deployments.
 Mentoring junior developers and improving code quality.
Laravel Developer — OneDios (Jun 2023 – Jul 2025, Noida)
 Improved API response time by 35%.
 Implemented RBAC, JWT auth, secure payment flows.
 Built integration APIs reducing manual work by 50%.
 Created reusable Vue components increasing dev speed by 25%.
Web Developer — Creatiwe Global Pvt. Ltd. (Dec 2022 – May 2023, New Delhi)
 Built custom e-commerce systems with Laravel + Vue.js.
Web Developer — Best Hawk Infosystems Pvt. Ltd. (May 2022 – Nov 2022, Noida)
 Reduced SQL execution time from 30s to 5s.
Full-Stack Developer — Freelance (Nov 2020 – May 2022)
 Delivered CRMs, ERPs, automation systems.
EDUCATION
BCA — GGSIPU (2019–2022)
 12th — NIOS (2018–2019)
 10th — CBSE (2015–2016)
ACHIEVEMENTS
 Reduced MySQL query time by 80%.
 Automated workflows saving 100+ hours monthly.
 Mentored juniors improving onboarding efficiency by 50%.`

const parsedData = parseComprehensiveResume(resumeText)
console.log('Parsed Resume Data:', JSON.stringify(parsedData, null, 2))

export default parsedData
