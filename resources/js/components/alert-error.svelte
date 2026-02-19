<script lang="ts">
  import { CircleAlert } from '@lucide/svelte';

  import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

  interface Props {
    errors: string[];
    title?: string;
  }

  const { errors, title = 'Something went wrong.' }: Props = $props();

  const uniqueErrors = $derived([...new Set(errors)]);
</script>

<Alert variant="destructive">
  <CircleAlert class="size-4" />
  <AlertTitle>{title}</AlertTitle>
  <AlertDescription>
    <ul class="list-inside list-disc text-sm">
      {#each uniqueErrors as error (error)}
        <li>{error}</li>
      {/each}
    </ul>
  </AlertDescription>
</Alert>
