import { fileURLToPath } from 'node:url';
import { includeIgnoreFile } from '@eslint/compat';
import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import svelte from 'eslint-plugin-svelte';
import unicorn from 'eslint-plugin-unicorn';
import { defineConfig } from 'eslint/config';
import globals from 'globals';
import ts from 'typescript-eslint';

const gitignorePath = fileURLToPath(new URL('./.gitignore', import.meta.url));

export default defineConfig([
  includeIgnoreFile(gitignorePath),
  js.configs.recommended,
  ...ts.configs.recommended,
  ...svelte.configs.recommended,
  prettier,
  {
    plugins: {
      unicorn,
    },
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
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
    },
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
  // TypeScript
  {
    files: ['**/*.ts'],
    rules: {
      // Allow explicit any
      '@typescript-eslint/no-explicit-any': 'off',
      // Less strict rules for Laravel/Inertia patterns
      '@typescript-eslint/no-unsafe-assignment': 'off',
      '@typescript-eslint/no-unsafe-call': 'off',
      '@typescript-eslint/no-unsafe-member-access': 'off',
    },
  },
  // Svelte
  {
    files: ['**/*.svelte', '**/*.svelte.ts', '**/*.svelte.js'],
    languageOptions: {
      parserOptions: {
        projectService: true,
        extraFileExtensions: ['.svelte'],
        parser: ts.parser,
      },
    },
    rules: {
      'svelte/no-navigation-without-resolve': [
        'error',
        {
          ignoreLinks: true,
        },
      ],
      // Less strict rules for Laravel/Inertia patterns
      '@typescript-eslint/no-explicit-any': 'off',
      '@typescript-eslint/no-unsafe-assignment': 'off',
      '@typescript-eslint/no-unsafe-call': 'off',
      '@typescript-eslint/no-unsafe-member-access': 'off',
    },
  },
]);
