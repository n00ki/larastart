<script lang="ts">
  import { cn } from '@/lib/utils';

  interface Props {
    class?: string;
  }

  const { class: className = '', ...props }: Props = $props();
</script>

<img
  {...props}
  src="https://res.cloudinary.com/nshemesh/image/upload/v1771517303/larastart/logo.png"
  class={cn('mx-auto size-16 md:size-20', className)}
/>
