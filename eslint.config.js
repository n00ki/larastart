import antfu from "@antfu/eslint-config";

export default antfu(
  {
    type: "app",
    typescript: true,
    svelte: true,

    stylistic: {
      indent: 2,
      quotes: "double",
      semi: true,
    },

    formatters: {
      css: true,
      html: true,
      markdown: true,
    },

    ignores: [
      "*.env",
      "**/node_modules/",
      "**/build/",
      "**/vendor/",
      "**/public/",
      "**/bootstrap/ssr/",
      "vite.config.ts",
      "resources/js/components/ui/*",
      "resources/views/mail/*",
      "resources/js/actions/**",
      "resources/js/routes/**",
      "resources/js/wayfinder/**",
      "docs/**",
    ],
  },

  // Global rules and Laravel-specific globals
  {
    languageOptions: {
      globals: {
        route: "readonly",
        Laravel: "readonly",
      },
    },
    rules: {
      "unicorn/filename-case": [
        "error",
        {
          case: "kebabCase",
          ignore: ["^\\.[a-z]+rc\\.(js|ts|json)$", "^[A-Z]+\\.(md|txt)$"],
        },
      ],
      "import/no-duplicates": "warn",
      "perfectionist/sort-imports": [
        "error",
        {
          type: "natural",
          order: "asc",
          ignoreCase: true,
          groups: [
            ["type", "builtin-type", "external-type", "internal-type"],
            ["builtin", "external"],
            "internal-components",
            "internal-layouts",
            "internal",
            "unknown",
          ],
          customGroups: {
            value: {
              "internal-components": "^@/components/",
              "internal-layouts": "^@/layouts/",
            },
            type: {
              "internal-components": "^@/components/",
              "internal-layouts": "^@/layouts/",
            },
          },
          newlinesBetween: "always",
          internalPattern: ["^@/"],
        },
      ],
    },
  },

  // TypeScript
  {
    files: ["**/*.ts"],
    rules: {
      // Allow explicit any
      "ts/no-explicit-any": "off",
      // Less strict rules for Laravel/Inertia patterns
      "ts/no-unsafe-assignment": "off",
      "ts/no-unsafe-call": "off",
      "ts/no-unsafe-member-access": "off",
    },
  },

  // Svelte
  {
    files: ["**/*.svelte", "**/*.svelte.ts", "**/*.svelte.js"],
    rules: {
      "svelte/infinite-reactive-loop": "error",
      "svelte/no-target-blank": "error",
      "svelte/prefer-class-directive": "error",
      "svelte/prefer-style-directive": "error",

      // Overrides
      "svelte/no-unknown-style-directive-property": "off",
      "prefer-const": "off",

      // Less strict rules for Laravel/Inertia patterns
      "ts/no-explicit-any": "off",
      "ts/no-unsafe-assignment": "off",
      "ts/no-unsafe-call": "off",
      "ts/no-unsafe-member-access": "off",
    },
    // TODO: monitor v4 support progress on https://github.com/francoismassart/eslint-plugin-tailwindcss/tree/alpha/v4
    // plugins: {
    //   tailwindcss,
    // },
    // settings: {
    //   tailwindcss: {
    //   },
    // },
  },
);
