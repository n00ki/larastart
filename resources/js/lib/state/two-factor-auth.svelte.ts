import type { CancelToken, UrlMethodPair } from '@inertiajs/core';

import { HttpCancelledError } from '@inertiajs/core';
import { useHttp } from '@inertiajs/svelte';
import { onDestroy } from 'svelte';
import { SvelteSet } from 'svelte/reactivity';

import { qrCode, recoveryCodes, secretKey } from '@/routes/two-factor';

export type TwoFactorAuth = ReturnType<typeof twoFactorAuthState>;

export function twoFactorAuthState() {
  const state = $state<{
    qrCodeSvg: string | null;
    manualSetupKey: string | null;
    recoveryCodesList: string[];
    errors: string[];
  }>({
    qrCodeSvg: null,
    manualSetupKey: null,
    recoveryCodesList: [],
    errors: [],
  });
  const http = useHttp({});
  const pending = new SvelteSet<CancelToken>();

  onDestroy(() => {
    for (const request of pending) request.cancel();
  });

  async function fetchSetupData() {
    state.errors = [];
    [state.qrCodeSvg, state.manualSetupKey] = await Promise.all([
      load(
        qrCode(),
        'Failed to fetch QR code',
        ({ svg }: { svg: string }) => svg,
      ),
      load(secretKey(), 'Failed to fetch a setup key', readSetupKey),
    ]);
  }

  async function fetchRecoveryCodes() {
    state.errors = [];
    state.recoveryCodesList =
      (await load(
        recoveryCodes(),
        'Failed to fetch recovery codes',
        (codes: string[]) => codes,
      )) ?? [];
  }

  function clearSetupData() {
    state.qrCodeSvg = null;
    state.manualSetupKey = null;
    state.errors = [];
  }

  async function load<Input, Value>(
    route: UrlMethodPair,
    message: string,
    read: (payload: Input) => Value,
  ): Promise<Value | null> {
    let request: CancelToken | undefined;
    try {
      const payload = await http.submit(route, {
        onCancelToken(token) {
          request = token;
          pending.add(token);
        },
      });
      return read(payload as Input);
    } catch (error) {
      if (
        !(error instanceof HttpCancelledError) &&
        !state.errors.includes(message)
      ) {
        state.errors.push(message);
      }
      return null;
    } finally {
      if (request) pending.delete(request);
    }
  }

  return {
    state,
    get hasSetupData() {
      return Boolean(state.qrCodeSvg && state.manualSetupKey);
    },
    clearSetupData,
    fetchSetupData,
    fetchRecoveryCodes,
  };
}

function readSetupKey(
  payload: string | { secretKey?: string; secret_key?: string },
) {
  const key =
    typeof payload === 'string'
      ? payload
      : (payload.secretKey ?? payload.secret_key);
  if (!key) throw new Error('Missing setup key');
  return key;
}
