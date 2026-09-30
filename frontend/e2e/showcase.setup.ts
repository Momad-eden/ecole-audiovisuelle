import { test as setup } from "@playwright/test";
import { artisan, isLocal } from "./showcase";

setup("créer les pages d'essai des héros", () => {
  setup.skip(!isLocal(), "Site distant : pas de pages d'essai.");
  artisan("emsi:hero-showcase");
});
