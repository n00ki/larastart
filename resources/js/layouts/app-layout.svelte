<script lang="ts">
  import type { BreadcrumbItem } from "@/types";
  import type { Snippet } from "svelte";

  import AppSidebarLayout from "@/layouts/app/app-sidebar-layout.svelte";
  import BaseLayout from "@/layouts/base-layout.svelte";

  interface Props {
    breadcrumbs?: BreadcrumbItem[];
    children?: Snippet;
  }

  const { breadcrumbs = [], children }: Props = $props();
</script>

<BaseLayout>
  <AppSidebarLayout {breadcrumbs}>
    {@render children?.()}
  </AppSidebarLayout>
</BaseLayout>
