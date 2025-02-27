<?php

namespace App\Objects;

use Composer\Semver\Semver;
use Illuminate\Support\Arr;

class GuesserFiles
{
    public const COMPOSER_FILE = "composer.json";
    public const ENV_FILE = ".env";
    public const ENV_DEFAULT_TEMPLATE_FILE = ".env.example";
    public const NVMRC_FILE = ".nvmrc";
    public const PACKAGE_FILE = "package.json";
    public const ARTISAN_FILE = "artisan";
    public const MIGRATIONS_DIR = "database" . DIRECTORY_SEPARATOR . "migrations";

    public const COMPOSER_VAR = "composerFile";
    public const ENV_VAR = "envFile";
    public const MIGRATIONS_VAR = "migrationsDir";
    public const ENV_DEFAULT_TEMPLATE_VAR = "envWorkflowFile";
    public const NVMRC_VAR = "nvmrcFile";
    public const PACKAGE_VAR = "packageFile";
    public const ARTISAN_VAR = "artisanFile";

    public array $filePaths = [];

    public function pathFiles(string $projectDir, string $optionEnvWorkflowFile = self::ENV_DEFAULT_TEMPLATE_FILE)
    {
        $arrayFiles = [
            self::COMPOSER_VAR => self::COMPOSER_FILE,
            self::ENV_VAR => self::ENV_FILE,
            self::ENV_DEFAULT_TEMPLATE_VAR => self::ENV_DEFAULT_TEMPLATE_FILE,
            self::NVMRC_VAR => self::NVMRC_FILE,
            self::PACKAGE_VAR => self::PACKAGE_FILE,
            self::ARTISAN_VAR => self::ARTISAN_FILE,
            self::MIGRATIONS_VAR => self::MIGRATIONS_DIR,
        ];
        foreach ($arrayFiles as $variable => $file) {
            $this->filePaths[$variable] = base_path($file);
        }
        if ($projectDir !== "") {
            foreach ($arrayFiles as $variable => $file) {
                $this->filePaths[$variable] = $projectDir . DIRECTORY_SEPARATOR . $file;
            }
        }
    }

    public function getComposerPath(): string
    {
        return Arr::get($this->filePaths, self::COMPOSER_VAR, "");
    }
    public function composerExists(): bool
    {
        $exists = $this->getComposerPath();
        if ($exists == "") {
            return false;
        }
        return is_file($this->getComposerPath());
    }

    public function getEnvPath(): string
    {
        return Arr::get($this->filePaths, self::ENV_VAR, "");
    }
    public function envExists(): bool
    {
        $exists = $this->getEnvPath();
        if ($exists == "") {
            return false;
        }
        return is_file($this->getEnvPath());
    }

    public function getEnvDefaultTemplatePath(): string
    {
        return Arr::get($this->filePaths, self::ENV_DEFAULT_TEMPLATE_VAR, "");
    }
    public function envDefaultTemplateExists(): bool
    {
        return $this->somethingExists("getEnvDefaultTemplatePath");
    }
    public function getPackagePath(): string
    {
        return Arr::get($this->filePaths, self::PACKAGE_VAR, "");
    }
    public function packageExists(): bool
    {
        return $this->somethingExists("getPackagePath");
    }
    public function getNvmrcPath(): string
    {
        return Arr::get($this->filePaths, self::NVMRC_VAR, "");
    }
    public function nvmrcExists(): bool
    {
        return $this->somethingExists("getNvmrcPath");
    }
    public function getArtisanPath(): string
    {
        return Arr::get($this->filePaths, self::ARTISAN_VAR, "");
    }
    public function artisanExists(): bool
    {
        return $this->somethingExists("getArtisanPath");
    }


    public function getMigrationsPath(): string
    {
        return Arr::get($this->filePaths, self::MIGRATIONS_VAR, "");
    }
    public function migrationsExists(): bool
    {
        return $this->somethingExists("getMigrationsPath", true);
    }



    private function somethingExists($methodPath, $isDirCheck = false): bool
    {
        $path = call_user_func([$this, $methodPath]);
        $exists = $path;
        if ($exists == "") {
            return false;
        }
        if ($isDirCheck) {
            return is_dir($path);
        }
        return is_file($path);
    }

    public static function detectLaravelVersionFromTestbench($testbenchVersion): array
    {
        $listLaravelVersions = [ "6.*", "7.*", "8.*"];
        $listTestBenchVersions = [ "4.0", "5.0", "6.0"];
        $stepLaravelVersions = [];
        $i = 0;

        try {
            foreach ($listTestBenchVersions as $testbench) {
                if (Semver::satisfies($testbench, $testbenchVersion)) {
                    $stepLaravelVersions[] = $listLaravelVersions[$i];
                }
                $i++;
            }
        } catch (\Exception $e) {
            $stepLaravelVersions = [];
        }
        //$this->ste = $stepPhp;
        return $stepLaravelVersions;
    }
}
