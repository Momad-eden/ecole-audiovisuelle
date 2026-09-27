import { cn } from "@/lib/utils";

export const inputClass =
  "w-full rounded-xl border border-line bg-night-2 px-4 py-3 text-ink placeholder:text-ink-muted/60 focus:border-amber focus:outline-none aria-[invalid=true]:border-rec";

export function Field({ id, label, error, hint, required, children, className }: { id: string; label: string; error?: string; hint?: string; required?: boolean; children: React.ReactNode; className?: string }) {
  return (
    <div className={cn("space-y-2", className)}>
      <label htmlFor={id} className="block text-sm font-medium">
        {label} {required && <span className="text-amber" aria-hidden>*</span>}
      </label>
      {children}
      {hint && !error && <p id={`${id}-hint`} className="text-xs text-ink-muted">{hint}</p>}
      {error && <p id={`${id}-error`} role="alert" className="text-sm text-rec">{error}</p>}
    </div>
  );
}

/** Champ piège anti-robots : invisible pour les humains et les lecteurs d'écran. */
export function Honeypot({ register }: { register: object }) {
  return (
    <div className="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
      <label htmlFor="website">Ne pas remplir</label>
      <input id="website" tabIndex={-1} autoComplete="off" {...register} />
    </div>
  );
}
