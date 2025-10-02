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
          <Link
            as="button"
            href={dashboard()}
            class="
              inline-block rounded-sm border border-border px-5 py-1.5 text-sm leading-normal text-foreground
              hover:border-border/70
            "
          >
            Dashboard
          </Link>
        {:else}
          <Link
            as="button"
            href={login()}
            class="
              inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-foreground
              hover:border-border
            "
          >
            Log in
          </Link>
          <Link
            as="button"
            href={register()}
            class="
              inline-block rounded-sm border border-border px-5 py-1.5 text-sm leading-normal text-foreground
              hover:border-border/70
            "
          >
            Register
          </Link>
        {/if}
      </nav>
    </header>

    <div class="absolute right-5 bottom-5">
      <ThemeSwitch />
    </div>

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
            class="bg-gradient-to-r from-blue-500 to-sky-400 bg-clip-text text-transparent"
            >mise en place</span
          > for your next Laravel + Svelte project 🚀
        </h2>
      </section>

      <section class="w-full max-w-3xl">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
          <a
            href="https://laravel.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Sparkles}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Laravel 12</span>
          </a>
          <a
            href="https://inertiajs.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={LinkIcon}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Inertia 2.0</span>
          </a>
          <a
            href="https://svelte.dev/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={WandSparkles}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Svelte 5</span>
          </a>
          <a
            href="https://tailwindcss.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Palette}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">TailwindCSS v4</span>
          </a>
          <a
            href="https://www.shadcn-svelte.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Paintbrush}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">shadcn-svelte</span>
          </a>
          <a
            href="https://typescriptlang.org/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={FileType}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">TypeScript</span>
          </a>
          <a
            href="https://pestphp.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={TestTubeDiagonal}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Pest 4</span>
          </a>
          <a
            href="https://github.com/laravel/wayfinder"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Globe}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Laravel Wayfinder</span>
          </a>
          <a
            href="https://phpstan.org/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={ScanEye}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">PHPStan</span>
          </a>
          <a
            href="https://laravel.com/docs/pint"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Hammer}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Laravel Pint</span>
          </a>
          <a
            href="https://eslint.org/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={Zap}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">ESLint</span>
          </a>
          <a
            href="https://getrector.org/"
            target="_blank"
            rel="noopener noreferrer"
            class="
              group flex items-center gap-3 rounded-lg border border-border p-4 transition-colors
              hover:bg-muted hover:shadow-sm
              focus-visible:outline-primary
            "
          >
            <Icon
              name={RefreshCw}
              size={18}
              class="text-muted-foreground group-hover:text-blue-400"
            />
            <span class="font-medium">Rector</span>
          </a>
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
