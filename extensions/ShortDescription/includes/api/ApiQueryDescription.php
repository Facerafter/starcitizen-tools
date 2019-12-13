<?php

/**
 * Extracted from WikiBase
 */

use ApiQuery;
use ApiQueryBase;
use Language;
use PageProps;
use Title;

/**
 * Provides a short description of the page in the content language.
 * The description may be taken from an upstream Wikibase instance, or from a parser function in
 * the article wikitext.
 *
 * Arguably this should be a separate extension so that it can be used on wikis without Wikibase
 * as well, but was initially implemented inside Wikibase for speed and convenience (T189154).
 *
 * @license GPL-2.0+
 */
class Description extends ApiQueryBase {

	/**
	 * Local description, in the form of a {{SHORTDESC:...}} parser function.
	 */
	const SOURCE_LOCAL = 'local';

	/**
	 * @var Language
	 */
	private $contentLanguage;

	/**
	 * @var EntityIdLookup
	 */
	private $idLookup;

	/**
	 * @var TermIndex
	 */
	private $termIndex;

	/**
	 * @param ApiQuery $query
	 * @param string $moduleName
	 * @param Language $contentLanguage
	 * @param EntityIdLookup $idLookup
	 * @param TermIndex $termIndex
	 */
	public function __construct(
		ApiQuery $query,
		$moduleName,
		Language $contentLanguage,
		EntityIdLookup $idLookup,
		TermIndex $termIndex
	) {
		parent::__construct( $query, $moduleName, 'desc' );
		$this->contentLanguage = $contentLanguage;
		$this->idLookup = $idLookup;
		$this->termIndex = $termIndex;
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$continue = $this->getParameter( 'continue' );

		$titlesByPageId = $this->getPageSet()->getGoodTitles();
		// Just in case we are dealing with titles from some very fast generator,
		// apply some limits as a sanity check.
		$limit = $this->getMain()->canApiHighLimits() ? self::LIMIT_BIG2 : self::LIMIT_BIG1;
		if ( $continue + $limit < count( $titlesByPageId ) ) {
			$this->setContinueEnumParameter( 'continue', $continue + $limit );
		}
		$titlesByPageId = array_slice( $titlesByPageId, $continue, $limit, true );

		$localDescriptionsByPageId = $this->getLocalDescriptions( $titlesByPageId );

		$this->addDataToResponse( array_keys( $titlesByPageId ),
			$localDescriptionsByPageId, $continue );
	}

	/**
	 * @param Title[] $titlesByPageId
	 *
	 * @return string[] Associative array of page ID => description.
	 */
	private function getLocalDescriptions( array $titlesByPageId ) {
		if ( !$titlesByPageId ) {
			return [];
		}
		return PageProps::getInstance()->getProperties( $titlesByPageId, 'shortdesc' );
	}

	/**
	 * @param int[] $pageIds Page IDs, in the same order as returned by the ApiPageSet.
	 * @param string[] $localDescriptionsByPageId Descriptions from local wikitext, as an
	 *   associative array of page ID => description in the content language.
	 * @param int $continue The API request is being continued from this position.
	 */
	private function addDataToResponse(
		array $pageIds,
		array $localDescriptionsByPageId,
		$continue
	) {
		$result = $this->getResult();
		$i = 0;
		$fit = true;
		foreach ( $pageIds as $pageId ) {
			$path = [ 'query', 'pages', $pageId ];
			if ( array_key_exists( $pageId, $localDescriptionsByPageId ) ) {
				$fit = $result->addValue( $path, 'description', $localDescriptionsByPageId[$pageId] )
					&& $result->addValue( $path, 'descriptionsource', 'local' );
			}
			if ( !$fit ) {
				$this->setContinueEnumParameter( 'continue', $continue + $i );
				break;
			}
			$i++;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getCacheMode( $params ) {
		return 'public';
	}

	/**
	 * @inheritDoc
	 */
	protected function getAllowedParams() {
		return [
			'continue' => [
				self::PARAM_HELP_MSG => 'api-help-param-continue',
				self::PARAM_TYPE => 'integer',
				self::PARAM_DFLT => 0,
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function getExamplesMessages() {
		return [
			'action=query&prop=description&titles=London'
			=> 'apihelp-query+description-example',
		];
	}

}