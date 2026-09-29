import { test as teardown } from "@playwright/test";
import { artisan, isLocal } from "./showcase";

teardown("supprimer les pages d'essai des héros et des nouveaux blocs", () => {
  teardown.skip(!isLocal(), "Site distant : pas de pages d'essai.");
  artisan("emsi:hero-showcase", "--remove");
  artisan("emsi:domains-showcase", "--remove");
});
