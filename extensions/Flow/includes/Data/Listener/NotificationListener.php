<?php

namespace Flow\Data\Listener;

use Flow\Exception\InvalidDataException;
use Flow\Exception\InvalidInputException;
use Flow\Model\AbstractRevision;
use Flow\Model\Header;
use Flow\Model\PostRevision;
use Flow\Model\PostSummary;
use Flow\Model\Workflow;
use Flow\NotificationController;

class NotificationListener extends AbstractListener {

	/**
	 * @var NotificationController
	 */
	protected $notificationController;

	public function __construct( NotificationController $notificationController ) {
		$this->notificationController = $notificationController;
	}

	public function onAfterInsert( $object, array $row, array $metadata ) {
		if ( !$object instanceof AbstractRevision ) {
			return;
		}

		if ( isset( $metadata['imported'] ) && $metadata['imported'] ) {
			// Don't send any notifications by default for imports
			return;
		}

		switch( $row['rev_change_type'] ) {
		// Actually new-topic @todo rename
		case 'new-post':
			if ( !isset(
				$metadata['board-workflow'],
				$metadata['workflow'],
				$metadata['topic-title'],
				$metadata['first-post']
			) ) {
				throw new InvalidDataException( 'Invalid metadata for revision ' . $object->getRevisionId()->getAlphadecimal(), 'missing-metadata' );
			}

			$this->notificationController->notifyNewTopic( array(
				'board-workflow' => $metadata['board-workflow'],
				'topic-workflow' => $metadata['workflow'],
				'topic-title' => $metadata['topic-title'],
				'first-post' => $metadata['first-post'],
			) );
			break;

		case 'edit-title':
			$this->notifyPostChange( 'flow-topic-renamed', $object, $metadata );
			break;

		case 'reply':
			$this->notifyPostChange( 'flow-post-reply', $object, $metadata );
			break;

		case 'edit-post':
			$this->notifyPostChange( 'flow-post-edited', $object, $metadata );
			break;

		case 'lock-topic':
			$this->notificationController->notifyTopicLocked( 'flow-topic-resolved', array(
				'revision' => $object,
				'topic-workflow' => $metadata['workflow'],
				'topic-title' => $metadata['topic-title'],
			) );
			break;

		// "restore" can be a lot of different things
		// - undo moderation (suppress/delete/hide) things
		// - undo lock status
		// we'll need to inspect the previous revision to figure out what is was
		case 'restore-topic':
			$post = $object->getCollection();
			$previousRevision = $post->getPrevRevision( $object );
			if ( $previousRevision->isLocked() ) {
				$this->notificationController->notifyTopicLocked( 'flow-topic-reopened', array(
					'revision' => $object,
					'topic-workflow' => $metadata['workflow'],
					'topic-title' => $metadata['topic-title'],
				) );
			}
			break;

		case 'edit-header':
			$this->notificationController->notifyHeaderChange( 'flow-description-edited', array(
				'revision' => $object,
				'board-workflow' => $metadata['workflow'],
			) );
			break;

		case 'create-topic-summary':
		case 'edit-topic-summary':
			$this->notificationController->notifySummaryChange( 'flow-summary-edited', array(
				'revision' => $object,
				'topic-workflow' => $metadata['workflow'],
				'topic-title' => $metadata['topic-title'],
			) );
			break;
		}
	}

	/**
	 * @param string $type
	 * @param PostRevision $object
	 * @param array $metadata
	 * @param array $params
	 * @throws InvalidDataException
	 */
	protected function notifyPostChange( $type, PostRevision $object, $metadata, array $params = array() ) {
		if ( !isset(
			$metadata['workflow'],
			$metadata['topic-title']
		) ) {
			throw new InvalidDataException( 'Invalid metadata for topic|post revision ' . $object->getRevisionId()->getAlphadecimal(), 'missing-metadata' );
		}

		$workflow = $metadata['workflow'];
		if ( !$workflow instanceof Workflow ) {
			throw new InvalidDataException( 'Workflow metadata is not a Workflow', 'missing-metadata' );
		}

		$this->notificationController->notifyPostChange( $type, $params + array(
			'revision' => $object,
			'topic-workflow' => $workflow,
			'topic-title' => $metadata['topic-title'],
		) );
	}
}
