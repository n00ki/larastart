import { useHttp } from '@inertiajs/svelte';

import { qrCode, recoveryCodes, secretKey } from '@/routes/two-factor';

type TwoFactorAuthState = {
  qrCodeSvg: string | null;
  manualSetupKey: string | null;
  recoveryCodesList: string[];
  errors: string[];
};

export type TwoFactorAuthStateApi = {
  state: TwoFactorAuthState;
  hasSetupData: () => boolean;
  clearSetupData: () => void;
  clearErrors: () => void;
  clearTwoFactorAuthData: () => void;
  fetchQrCode: () => Promise<void>;
  fetchSetupKey: () => Promise<void>;
  fetchSetupData: () => Promise<void>;
  fetchRecoveryCodes: () => Promise<void>;
};

const state = $state<TwoFactorAuthState>({
  qrCodeSvg: null,
  manualSetupKey: null,
  recoveryCodesList: [],
  errors: [],
});

const hasSetupData = (): boolean =>
  state.qrCodeSvg !== null && state.manualSetupKey !== null;

export function twoFactorAuthState(): TwoFactorAuthStateApi {
  const http = useHttp<Record<string, never>, unknown>({});

  const addError = (message: string): void => {
    if (!state.errors.includes(message)) {
      state.errors = [...state.errors, message];
    }
  };

  const reportFailure = (message: string) => ({
    onHttpException: () => addError(message),
    onNetworkError: () => addError(message),
  });

  const fetchQrCode = async (): Promise<void> => {
    const message = 'Failed to fetch QR code';

    try {
      const { svg } = (await http.submit(qrCode(), reportFailure(message))) as {
        svg: string;
        url: string;
      };
      state.qrCodeSvg = svg;
    } catch {
      addError(message);
      state.qrCodeSvg = null;
    }
  };

  const fetchSetupKey = async (): Promise<void> => {
    const message = 'Failed to fetch a setup key';

    try {
      const payload = (await http.submit(
        secretKey(),
        reportFailure(message),
      )) as { secretKey?: string; secret_key?: string } | string;
      const key =
        typeof payload === 'string'
          ? payload
          : (payload.secretKey ?? payload.secret_key ?? null);

      if (!key) {
        throw new Error('Setup key not found in response');
      }

      state.manualSetupKey = key;
    } catch {
      addError(message);
      state.manualSetupKey = null;
    }
  };

  const clearErrors = (): void => {
    state.errors = [];
  };

  const clearSetupData = (): void => {
    state.manualSetupKey = null;
    state.qrCodeSvg = null;
    clearErrors();
  };

  const clearTwoFactorAuthData = (): void => {
    clearSetupData();
    state.recoveryCodesList = [];
    clearErrors();
  };

  const fetchRecoveryCodes = async (): Promise<void> => {
    const message = 'Failed to fetch recovery codes';

    try {
      clearErrors();
      state.recoveryCodesList = (await http.submit(
        recoveryCodes(),
        reportFailure(message),
      )) as string[];
    } catch {
      addError(message);
      state.recoveryCodesList = [];
    }
  };

  const fetchSetupData = async (): Promise<void> => {
    clearErrors();
    await Promise.all([fetchQrCode(), fetchSetupKey()]);
  };

  return {
    state,
    hasSetupData,
    clearSetupData,
    clearErrors,
    clearTwoFactorAuthData,
    fetchQrCode,
    fetchSetupKey,
    fetchSetupData,
    fetchRecoveryCodes,
  };
}
