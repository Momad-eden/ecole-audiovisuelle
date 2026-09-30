import { execFileSync } from "node:child_process";
import path from "node:path";

/**
 * Pages d'essai des héros (Projection, Studio, Cinéma) et des nouveaux blocs (triptyque, formations du campus,
 * documents, « Nous soutenir ») : créées dans la base locale avant les
 * parcours (showcase.setup.ts) et supprimées après (showcase.teardown.ts). Rien contre un site distant.
 */
export const isLocal = () => ["localhost", "127.0.0.1"].includes(new URL(process.env.E2E_BASE_URL ?? "http://localhost:3000").hostname);

export function artisan(...args: string[]) {
  execFileSync("php", ["artisan", ...args], { cwd: path.resolve(__dirname, "../.."), stdio: "inherit" });
}
