<?php

use Composer\Installer\PackageEvent;

class ComposerEventListener {

	public static function prePackageInstall(PackageEvent $event){
		$compser = $event->getComposer();
		var_dump("Preparing to install packages");
	}

	public static function postPackageInstall(PackageEvent $event){
		$compser = $event->getComposer();
		var_dump("Package install now going to generate lock file");
	}

}