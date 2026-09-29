import { test as teardown } from "@playwright/test";
import { artisan, isLocal } from "./showcase";

// Chaque suppression est tentée même si l'autre échoue ; le nettoyage échoue ensuite si l'une a échoué.
teardown("supprimer les pages d'essai des héros et des nouveaux blocs", () => {
  teardown.skip(!isLocal(), "Site distant : pas de pages d'essai.");
  const failures: string[] = [];
  for (const command of ["emsi:hero-showcase", "emsi:domains-showcase"]) {
    try {
      artisan(command, "--remove");
    } catch (error) {
      failures.push(`${command} --remove : ${error instanceof Error ? error.message : String(error)}`);
    }
  }
  if (failures.length > 0) throw new Error(`Nettoyage incomplet :\n${failures.join("\n")}`);
});
