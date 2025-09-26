# Frontend Setup (React + TypeScript + Vite + Tailwind + shadcn/ui)

## Prerequisites
- Node.js 18+
- PNPM (recommended) or npm/yarn

## Project Creation
```bash
cd frontend
pnpm create vite@latest . --template react-ts
pnpm install
```

## TailwindCSS Installation
```bash
pnpm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```
Configure `tailwind.config.cjs` (or .js):
```js
module.exports = {
  content: [
    './index.html',
    './src/**/*.{ts,tsx}',
  ],
  theme: { extend: {} },
  plugins: [],
};
```
Add to `src/index.css`:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## shadcn/ui (Later Token Integration)
```bash
pnpm dlx shadcn-ui@latest init
```
Add components as needed:
```bash
pnpm shadcn-ui add button card input table dialog dropdown-menu badge
```

## State & Data Layer
Install libs:
```bash
pnpm install @tanstack/react-query axios react-hook-form zod @hookform/resolvers jotai
```
Query Client setup (`src/lib/query.ts`):
```ts
import { QueryClient } from '@tanstack/react-query';
export const queryClient = new QueryClient();
```

## API Client
Create `src/lib/api.ts`:
```ts
import axios from 'axios';
export const api = axios.create({ baseURL: 'http://localhost:8000/api/v1', withCredentials: true });
```
Add interceptor for auth errors later.

## Auth Flow (Sanctum)
1. Preflight GET `/sanctum/csrf-cookie` (if using cookie session).
2. POST `/auth/login` with credentials.
3. Store minimal user state in global store.
4. Use React Query for `useMe()` fetching `/users/me`.

## Folder Structure
```
src/
  components/
  pages/
  features/
    auth/
    dashboard/
    content/
    assessments/
  lib/
  hooks/
  types/
```

## Routing
Install react-router:
```bash
pnpm install react-router-dom
```
Create routes with layout components (public vs protected).

## Environment Variables
`.env` (Vite):
```
VITE_API_BASE_URL=http://localhost:8000/api/v1
```
Use via `import.meta.env.VITE_API_BASE_URL`.

## UI Tokens Replacement (Later)
- When Figma tokens ready: integrate `@tokens/json` file.
- Map to Tailwind theme extend (colors, spacing, fontFamily, boxShadow).
- Provide `ThemeProvider` to switch (dark/light if needed).

## Linting & Formatting
```bash
pnpm install -D eslint @typescript-eslint/parser @typescript-eslint/eslint-plugin prettier eslint-config-prettier
```
Config sample `.eslintrc.cjs`:
```js
module.exports = {
  parser: '@typescript-eslint/parser',
  extends: ['eslint:recommended','plugin:@typescript-eslint/recommended','prettier'],
};
```

## Example Protected Route Wrapper
```tsx
import { Navigate } from 'react-router-dom';
import { useMe } from './hooks/useMe';
export function Protected({ children }: { children: JSX.Element }) {
  const { data: user, isLoading } = useMe();
  if (isLoading) return <div>Loading...</div>;
  if (!user) return <Navigate to="/login" replace />;
  return children;
}
```

## Basic Dashboard Placeholder
`src/features/dashboard/StudentDashboard.tsx`
```tsx
export function StudentDashboard() {
  return <div className="space-y-4">
    <h1 className="text-xl font-semibold">Student Dashboard</h1>
    <div className="grid gap-4 md:grid-cols-3">
      <div className="p-4 border rounded">Attendance</div>
      <div className="p-4 border rounded">Upcoming Assessments</div>
      <div className="p-4 border rounded">Fees Summary</div>
    </div>
  </div>;
}
```

## Development Scripts
Add to `package.json`:
```json
"scripts": {
  "dev": "vite",
  "build": "tsc && vite build",
  "preview": "vite preview"
}
```

## Testing (Optional Early)
Install Vitest:
```bash
pnpm install -D vitest jsdom @testing-library/react @testing-library/user-event @testing-library/jest-dom
```

## Next Frontend Actions
1. Implement auth pages (login, logout).
2. Add role-based dashboard routing.
3. Integrate content listing endpoints.
4. Build assessment list & submission forms.

(End of frontend setup doc)
