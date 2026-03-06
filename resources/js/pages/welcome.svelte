<script lang="ts">
  import { Link, page } from '@inertiajs/svelte';
  import {
    FileType,
    Github,
    Globe,
    Hammer,
    Link as LinkIcon,
    Paintbrush,
    Palette,
    RefreshCw,
    ScanEye,
    Sparkles,
    TestTubeDiagonal,
    WandSparkles,
    Zap,
  } from '@lucide/svelte';

  import BaseLayout from '@/layouts/base-layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import Icon from '@/components/icon.svelte';
  import ThemeSwitch from '@/components/theme-switch.svelte';
  import { Button } from '@/components/ui/button';

  import { dashboard, login, register } from '@/routes';

  const featureCardClass =
    'group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors hover:bg-muted hover:shadow-sm focus-visible:outline-primary';

  const primaryAuthLinkClass =
    'inline-block rounded-sm border border-border px-5 py-1.5 text-sm leading-normal text-foreground hover:border-border/70';

  const secondaryAuthLinkClass =
    'inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-foreground hover:border-border';

  const featureCards = [
    { href: 'https://laravel.com/', icon: Sparkles, label: 'Laravel 12' },
    { href: 'https://inertiajs.com/', icon: LinkIcon, label: 'Inertia 2.0' },
    { href: 'https://svelte.dev/', icon: WandSparkles, label: 'Svelte 5' },
    {
      href: 'https://tailwindcss.com/',
      icon: Palette,
      label: 'TailwindCSS v4',
    },
    {
      href: 'https://www.shadcn-svelte.com/',
      icon: Paintbrush,
      label: 'shadcn-svelte',
    },
    {
      href: 'https://typescriptlang.org/',
      icon: FileType,
      label: 'TypeScript',
    },
    { href: 'https://pestphp.com/', icon: TestTubeDiagonal, label: 'Pest 4' },
    {
      href: 'https://github.com/laravel/wayfinder',
      icon: Globe,
      label: 'Laravel Wayfinder',
    },
    { href: 'https://phpstan.org/', icon: ScanEye, label: 'PHPStan' },
    {
      href: 'https://laravel.com/docs/pint',
      icon: Hammer,
      label: 'Laravel Pint',
    },
    { href: 'https://eslint.org/', icon: Zap, label: 'ESLint' },
    { href: 'https://getrector.org/', icon: RefreshCw, label: 'Rector' },
  ];

  interface Props {
    canRegister: boolean;
  }

  const { canRegister }: Props = $props();
  const user = $derived($page.props.auth.user);
</script>

<AppHead
  title="Welcome"
  description="LaraStart :: the ultimate mise en place for your next Laravel + Svelte project"
/>

<BaseLayout>
  <div class="flex min-h-screen flex-col items-center gap-8 px-4 py-8">
    <header class="flex w-full max-w-3xl items-center justify-between">
      <div>
        <Button
          href="https://github.com/n00ki/larastart"
          target="_blank"
          rel="noopener noreferrer"
          variant="outline"
          class="
            transition-none
          "
        >
          <Icon name={Github} size={16} />
        </Button>
      </div>
      <nav class="flex items-center justify-end gap-4">
        {#if user}
          <Link as="button" href={dashboard()} class={primaryAuthLinkClass}>
            Dashboard
          </Link>
        {:else}
          <Link as="button" href={login()} class={secondaryAuthLinkClass}>
            Log in
          </Link>
          {#if canRegister}
            <Link as="button" href={register()} class={primaryAuthLinkClass}>
              Register
            </Link>
          {/if}
        {/if}
      </nav>
    </header>

    <ThemeSwitch class="fixed right-5 bottom-5 z-10" />

    <div
      class="flex w-full max-w-3xl flex-1 flex-col items-center justify-center gap-8"
    >
      <section class="text-center">
        <img
          src="https://res.cloudinary.com/nshemesh/image/upload/v1756715372/larastart/logo.png"
          alt="logo"
          class="
            mx-auto size-16
            md:size-24
          "
        />
        <h1 class="mb-1.5 text-3xl font-black md:text-6xl">LaraStart</h1>
        <h2 class="text-base font-medium tracking-tight md:text-lg">
          the ultimate <span
            class="bg-linear-to-r from-blue-500 to-sky-400 bg-clip-text text-transparent"
            >mise en place</span
          > for your next Laravel + Svelte project 🚀
        </h2>
      </section>

      <section class="w-full max-w-3xl">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
          {#each featureCards as card (card.label)}
            <a
              href={card.href}
              target="_blank"
              rel="noopener noreferrer"
              class={featureCardClass}
            >
              <Icon
                name={card.icon}
                size={18}
                class="text-muted-foreground group-hover:text-blue-400"
              />
              <span class="font-medium">{card.label}</span>
            </a>
          {/each}
        </div>
      </section>

      <section class="w-full max-w-3xl">
        <h2 class="text-sm font-light">
          Inspired by the <a
            href="https://rubyonrails.org/doctrine#omakase"
            target="_blank"
            rel="noopener noreferrer"
            class="text-muted-foreground"
          >
            Rails doctrine
          </a>, <em>LaraStart</em> offers an opinionated selection of tools and a
          structured approach for your next web application. It provides a robust
          foundation with carefully chosen defaults, while preserving the flexibility
          to customize and extend as your project needs evolve.
        </h2>
      </section>
    </div>
  </div>
</BaseLayout>
