declare module 'ziggy-js' {
  export type RouteName = string;
  export function route(
    name?: string,
    params?: Record<string, unknown>,
    absolute?: boolean,
    config?: Record<string, unknown>
  ): string;
  export function useRoute(config?: Record<string, unknown>): typeof route;
}