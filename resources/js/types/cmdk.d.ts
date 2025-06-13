declare module "cmdk" {
  // Re-exportiere die offiziellen Typen aus dem veröffentlichen Build.
  // Diese Datei dient lediglich als Fallback, falls der Resolver die
  // entsprechenden .d.ts Dateien im Paket nicht automatisch findet.
  export * from "cmdk/dist/index";
  export { default } from "cmdk/dist/index";
}