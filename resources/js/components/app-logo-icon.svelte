<script lang="ts">
  interface Props {
    class?: string;
  }

  const { ...props }: Props = $props();
</script>

<img
  {...props}
  src="https://res.cloudinary.com/nshemesh/image/upload/v1771517303/larastart/logo.png"
  class="
    mx-auto size-16
    md:size-20
  "
/>
