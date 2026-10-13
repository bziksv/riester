<?php
class CNigesCookiesAcceptPublic
{
	/**
	 * Inject cookie notice at the end of page generation.
	 */
	public static function OnEpilog()
	{
		global $APPLICATION;

		// Banner is printed in the site footer before </body>. Epilog runs after
		// </html>, which produced a sitewide “Stray start tag div” for validators.
		if (defined('RIESTER_COOKIES_BANNER_RENDERED') && RIESTER_COOKIES_BANNER_RENDERED) {
			return;
		}

		if (!CModule::IncludeModule(cookiesaccept_MODULE_ID)) {
			return;
		}

		if (COption::GetOptionString(cookiesaccept_MODULE_ID, 'ACTIVE', 'N', SITE_ID) !== 'Y') {
			return;
		}

		if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE === true) {
			return;
		}
		if (isset($_REQUEST['ajax']) && (string)$_REQUEST['ajax'] !== '') {
			return;
		}
		if (isset($_REQUEST['bxajaxid']) && (string)$_REQUEST['bxajaxid'] !== '') {
			return;
		}

		$APPLICATION->IncludeComponent(
			'niges:cookiesaccept',
			'.default',
			array(),
			false,
			array('HIDE_ICONS' => 'Y')
		);
	}
}
