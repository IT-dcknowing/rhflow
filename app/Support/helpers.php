<?php

if (!function_exists('formatPrice')) {
    /**
     * Formater un prix selon la devise de l'utilisateur
     *
     * @param float|int|string $amount
     * @param array $currencyDetails  Ex: ['symbol' => '€', 'position' => 'after']
     * @return string
     */
    function formatPrice($amount, array $currencyDetails = [])
    {
        // 1) Récupérer la devise par défaut depuis l'utilisateur connecté si aucun détail n'est fourni
        if (empty($currencyDetails) && function_exists('auth') && auth()->check()) {
            try {
                // Accessor User::getCurrencyAttribute() retourne un tableau ['symbol','position',...]
                $currencyDetails = auth()->user()->currency ?? [];
            } catch (\Throwable $e) {
                $currencyDetails = [];
            }
        }

        // 2) Compléter depuis le référentiel User::getAvailableCurrencies() si nécessaire
        if (!isset($currencyDetails['symbol']) || !isset($currencyDetails['position'])) {
            try {
                $available = \App\Models\User::getAvailableCurrencies();
                // Si un code est fourni
                if (isset($currencyDetails['code']) && isset($available[$currencyDetails['code']])) {
                    $currencyDetails = array_merge($available[$currencyDetails['code']], $currencyDetails);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 3) Valeurs par défaut robustes
        $symbol = $currencyDetails['symbol'] ?? 'XOF';
        $position = $currencyDetails['position'] ?? 'after'; // 'before' | 'after'
        $includeSymbol = array_key_exists('include_symbol', $currencyDetails) ? (bool)$currencyDetails['include_symbol'] : true;
        $decimals = isset($currencyDetails['decimals'])
            ? (int)$currencyDetails['decimals']
            : (($position === 'after') ? 0 : 2);

        // 4) Normaliser le montant
        $numericAmount = is_numeric($amount) ? (float) $amount : 0.0;
        $formatted = number_format($numericAmount, $decimals, ',', ' ');

        if (!$includeSymbol) {
            return $formatted;
        }

        // 5) Appliquer le symbole selon la position
        return $position === 'after'
            ? $formatted . ' ' . $symbol
            : $symbol . ' ' . $formatted;
    }

    function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }

    function isModuleActive(string $moduleAlias): bool
    {
        $moduleService = app(\App\Services\ModuleService::class);
        return $moduleService->isModuleActive($moduleAlias);
    }

    function getActiveModules(): array
    {
        $moduleService = app(\App\Services\ModuleService::class);
        return $moduleService->getActiveModules();
    }
}
