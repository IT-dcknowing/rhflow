<?php

if (!function_exists('isModuleActive')) {
    /**
     * Vérifier si un module est actif pour l'utilisateur connecté
     */
    function isModuleActive(string $moduleAlias): bool
    {
        $moduleService = app(\App\Services\ModuleService::class);
        return $moduleService->isModuleActive($moduleAlias);
    }
}

if (!function_exists('getActiveModules')) {
    /**
     * Obtenir la liste des modules actifs pour l'utilisateur connecté
     */
    function getActiveModules(): array
    {
        $moduleService = app(\App\Services\ModuleService::class);
        return $moduleService->getActiveModules();
    }
}
