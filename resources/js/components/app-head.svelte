<script lang='ts'>
  import { page } from '@inertiajs/svelte';

  interface Props {
    title?: string;
    description?: string;
    image?: string;
    url?: string;
    type?: 'website' | 'article' | 'profile';
    site?: string;
    creator?: string;
  }

  const {
    title,
    description = 'LaraStart - the ultimate mise en place for your next Laravel + Svelte project',
    image = 'https://res.cloudinary.com/nshemesh/image/upload/v1756713825/larastart/meta.png',
    url,
    type = 'website',
  }: Props = $props();

  const fullTitle = $derived(title ? `${title} | LaraStart` : 'LaraStart');
  const canonicalUrl = $derived(url || $page.url);
  const imageUrl = $derived(
    image.startsWith('http') ? image : `${$page.props.app_url}${image}`,
  );
</script>

<svelte:head>
  <title>{fullTitle}</title>
  <meta name='description' content={description} />
  <link rel='canonical' href={canonicalUrl} />
  <meta name='image' content={image} />
  <meta name='robots' content='index, follow' />

  <!-- Open Graph tags -->
  <meta property='og:type' content={type} />
  <meta property='og:title' content={fullTitle} />
  <meta property='og:description' content={description} />
  <meta property='og:image' content={imageUrl} />
  <meta property='og:url' content={canonicalUrl} />
  <meta property='og:site_name' content='LaraStart' />
  <meta property='og:locale' content='en_US' />

  <!-- Twitter Card tags -->
  <meta name='twitter:card' content='summary_large_image' />
  <meta property='twitter:url' content={canonicalUrl} />
  <meta name='twitter:title' content={fullTitle} />
  <meta name='twitter:description' content={description} />
  <meta name='twitter:image' content={imageUrl} />
</svelte:head>
