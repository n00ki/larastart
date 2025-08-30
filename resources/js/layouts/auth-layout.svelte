<script lang="ts">
  import type { Snippet } from "svelte";

  import ThemeSwitch from "@/components/theme-switch.svelte";

  import AuthSimpleLayout from "@/layouts/auth/auth-simple-layout.svelte";
  import BaseLayout from "@/layouts/base-layout.svelte";

  interface Props {
    title: string;
    description: string;
    children?: Snippet;
  }

  const { title, description, children }: Props = $props();
</script>

<BaseLayout>
  <AuthSimpleLayout {title} {description}>
    {@render children?.()}

    <div class="absolute top-5 right-5">
      <ThemeSwitch />
    </div>
  </AuthSimpleLayout>
</BaseLayout>
