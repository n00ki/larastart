import type { PageProps as AppPageProps } from "./";
import type { PageProps as InertiaPageProps } from "@inertiajs/core";
import type { AxiosInstance } from "axios";
import type { route as routeFn } from "ziggy-js";

declare global {
  const route: typeof routeFn;
  interface Window {
    axios: AxiosInstance;
  }
}

declare module "@inertiajs/core" {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
