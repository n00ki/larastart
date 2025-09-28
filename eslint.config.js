import antfu from '@antfu/eslint-config'
import prettier from 'eslint-config-prettier'

export default antfu(
  {
    type: 'app',
    typescript: true,
    svelte: true,

    ignores: [
      '*.env',
      '**/node_modules/',
      '**/build/',
      '**/vendor/',
      '**/public/',
      '**/bootstrap/ssr/',
      'vite.config.ts',
      'resources/js/components/ui/*',
      'resources/views/mail/*',
      'resources/js/actions/**',
      'resources/js/routes/**',
      'resources/js/wayfinder/**',
      'docs/**',
    ],
  },

  // Avoid conflicts with prettier
  prettier,

  // Global rules and Laravel-specific globals
  {
    languageOptions: {
      globals: {
        route: 'readonly',
        Laravel: 'readonly',
      },
    },
    rules: {
      'unicorn/filename-case': [
        'error',
        {
          case: 'kebabCase',
          ignore: ['^\\.[a-z]+rc\\.(js|ts|json)$', '^[A-Z]+\\.(md|txt)$'],
        },
      ],
      'import/no-duplicates': 'warn',
    },
  },

  // TypeScript
  {
    files: ['**/*.ts'],
    rules: {
      // Allow explicit any
      'ts/no-explicit-any': 'off',
      // Less strict rules for Laravel/Inertia patterns
      'ts/no-unsafe-assignment': 'off',
      'ts/no-unsafe-call': 'off',
      'ts/no-unsafe-member-access': 'off',
    },
  },

  // Svelte
  {
    files: ['**/*.svelte', '**/*.svelte.ts', '**/*.svelte.js'],
    rules: {
      'svelte/infinite-reactive-loop': 'error',
      'svelte/no-target-blank': 'error',
      'svelte/prefer-class-directive': 'error',
      'svelte/prefer-style-directive': 'error',

      // Overrides
      'svelte/no-unknown-style-directive-property': 'off',
      'prefer-const': 'off',

      // Less strict rules for Laravel/Inertia patterns
      'ts/no-explicit-any': 'off',
      'ts/no-unsafe-assignment': 'off',
      'ts/no-unsafe-call': 'off',
      'ts/no-unsafe-member-access': 'off',
    },
  },
)
