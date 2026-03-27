<script module lang="ts">
  export { default as layout } from '@/layouts/base-layout.svelte';
</script>

<script lang="ts">
  import { Link, page } from '@inertiajs/svelte';
  import { ArrowLeft, RefreshCw } from '@lucide/svelte';

  import AppHead from '@/components/app-head.svelte';
  import AppLogoIcon from '@/components/app-logo-icon.svelte';
  import Icon from '@/components/icon.svelte';
  import { buttonVariants } from '@/components/ui/button';

  import { dashboard, home } from '@/routes';

  interface Props {
    status: 403 | 404 | 500 | 503;
  }

  const { status }: Props = $props();

  const user = $derived(page.props.auth.user);

  const contentByStatus: Record<
    Props['status'],
    { title: string; description: string }
  > = {
    403: {
      title: 'Forbidden',
      description: 'You do not have permission to access this page.',
    },
    404: {
      title: 'Not found',
      description: 'The page you are looking for could not be found.',
    },
    500: {
      title: 'Server error',
      description:
        'Something went wrong on our side. Please try again in a moment.',
    },
    503: {
      title: 'Unavailable',
      description:
        'The application is temporarily unavailable while maintenance is in progress.',
    },
  };

  const content = $derived(contentByStatus[status]);
  const returnHref = $derived(user ? dashboard() : home());
  const returnLabel = $derived(user ? 'Back to dashboard' : 'Back to home');
</script>

<AppHead title={`${status} ${content.title}`} />

<div class="flex min-h-screen items-center justify-center px-6 py-16">
  <div class="w-full max-w-3xl">
    <div class="mb-6">
      <AppLogoIcon />
    </div>

    <div class="text-center">
      <p
        class="mb-3 text-xs font-semibold tracking-[0.36em] text-muted-foreground uppercase"
      >
        {content.title}
      </p>

      <div
        class="mb-4 text-7xl font-black tracking-[-0.08em] text-foreground sm:text-8xl md:text-[9rem]"
      >
        {status}
      </div>

      <p
        class="mx-auto max-w-xl text-base leading-7 text-muted-foreground sm:text-lg"
      >
        {content.description}
      </p>

      <div
        class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row"
      >
        <Link
          href={returnHref}
          class={buttonVariants({ variant: 'default', size: 'lg' })}
        >
          <Icon name={ArrowLeft} size={16} />
          {returnLabel}
        </Link>

        <button
          type="button"
          class={buttonVariants({ variant: 'ghost', size: 'lg' })}
          onclick={() => window.location.reload()}
        >
          <Icon name={RefreshCw} size={16} />
          Try again
        </button>
      </div>
    </div>
  </div>
</div>
