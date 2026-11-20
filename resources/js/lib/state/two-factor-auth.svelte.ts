import { HttpCancelledError } from '@inertiajs/core';
import { useForm, useHttp } from '@inertiajs/svelte';

import { qrCode, secretKey } from '@/routes/two-factor';

export type TwoFactorAuth = ReturnType<typeof twoFactorAuthState>;

export function twoFactorAuthState() {
  const state = $state<{
    qrCodeSvg: string | null;
    manualSetupKey: string | null;
    errors: string[];
  }>({
    qrCodeSvg: null,
    manualSetupKey: null,
    errors: [],
  });
  const qrCodeRequest = useHttp<Record<string, never>, { svg: string }>({});
  const setupKeyRequest = useHttp<Record<string, never>, { secretKey: string }>(
    {},
  );
  // Keep confirmation state when the dialog closes.
  const confirmForm = useForm({ code: '' });

  async function fetchQrCode() {
    try {
      state.qrCodeSvg = (await qrCodeRequest.submit(qrCode())).svg;
    } catch (error) {
      if (!(error instanceof HttpCancelledError)) {
        state.errors.push('Failed to fetch QR code');
      }
    }
  }

  async function fetchSetupKey() {
    try {
      state.manualSetupKey = (
        await setupKeyRequest.submit(secretKey())
      ).secretKey;
    } catch (error) {
      if (!(error instanceof HttpCancelledError)) {
        state.errors.push('Failed to fetch a setup key');
      }
    }
  }

  function fetchSetupData() {
    state.errors = [];
    if (!state.qrCodeSvg && !qrCodeRequest.processing) fetchQrCode();
    if (!state.manualSetupKey && !setupKeyRequest.processing) {
      fetchSetupKey();
    }
  }

  function clearSetupData() {
    qrCodeRequest.cancel();
    setupKeyRequest.cancel();
    state.qrCodeSvg = null;
    state.manualSetupKey = null;
    state.errors = [];
  }

  return {
    state,
    confirmForm,
    get canContinueSetup() {
      return Boolean(
        (state.qrCodeSvg && state.manualSetupKey) ||
        confirmForm.processing ||
        confirmForm.errors.code,
      );
    },
    clearSetupData,
    fetchSetupData,
  };
}
