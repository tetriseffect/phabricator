<?php

final class PhabricatorCountdownTransaction
  extends PhabricatorApplicationTransaction {

  const TYPE_TITLE = 'countdown:title';
  const TYPE_EPOCH = 'countdown:epoch';
  const TYPE_DESCRIPTION = 'countdown:description';

  const MAILTAG_TITLE = 'countdown:title';
  const MAILTAG_EPOCH = 'countdown:epoch';
  const MAILTAG_DESCRIPTION = 'countdown:description';
  const MAILTAG_OTHER  = 'countdown:other';

  public function getApplicationName() {
    return 'countdown';
  }

  public function getApplicationTransactionType() {
    return PhabricatorCountdownCountdownPHIDType::TYPECONST;
  }

  public function getApplicationTransactionCommentObject() {
    return null;
  }

  public function getTitle() {
    $author_phid = $this->getAuthorPHID();
    $object_phid = $this->getObjectPHID();

    $old = $this->getOldValue();
    $new = $this->getNewValue();

    $type = $this->getTransactionType();
    switch ($type) {
      case self::TYPE_TITLE:
        if ($old === null) {
          return pht(
            '%s created this countdown.',
            $this->renderHandleLink($author_phid));
        } else {
          return pht(
            '%s renamed this countdown from "%s" to "%s".',
            $this->renderHandleLink($author_phid),
            $old,
            $new);
        }
      break;
      case self::TYPE_DESCRIPTION:
        if ($old === null) {
          return pht(
            '%s set the description of this countdown.',
            $this->renderHandleLink($author_phid));
        } else {
          return pht(
            '%s edited the description of this countdown.',
            $this->renderHandleLink($author_phid));
        }
      break;
      case self::TYPE_EPOCH:
        if ($old === null) {
          return pht(
            '%s set this countdown to end on %s.',
            $this->renderHandleLink($author_phid),
            phabricator_datetime($new, $this->getViewer()));
        } else if ($old != $new) {
          return pht(
            '%s updated this countdown to end on %s.',
            $this->renderHandleLink($author_phid),
            phabricator_datetime($new, $this->getViewer()));
        }
        break;
    }

    return parent::getTitle();
  }

  public function getTitleForFeed() {
    $author_phid = $this->getAuthorPHID();
    $object_phid = $this->getObjectPHID();

    $old = $this->getOldValue();
    $new = $this->getNewValue();

    $type = $this->getTransactionType();
    switch ($type) {
      case self::TYPE_TITLE:
        if ($old === null) {
          return pht(
            '%s created %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));

        } else {
          return pht(
            '%s renamed %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));
        }
      break;
      case self::TYPE_DESCRIPTION:
        if ($old === null) {
          return pht(
            '%s set the description of %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));

        } else {
          return pht(
            '%s edited the description of %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));
        }
      break;
      case self::TYPE_EPOCH:
        if ($old === null) {
          return pht(
            '%s set the end date of %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));

        } else {
          return pht(
            '%s edited the end date of %s.',
            $this->renderHandleLink($author_phid),
            $this->renderHandleLink($object_phid));
        }
      break;
    }

    return parent::getTitleForFeed();
  }

  public function getMailTags() {
    $tags = parent::getMailTags();

    switch ($this->getTransactionType()) {
      case self::TYPE_TITLE:
        $tags[] = self::MAILTAG_TITLE;
        break;
      case self::TYPE_EPOCH:
        $tags[] = self::MAILTAG_EPOCH;
        break;
      case self::TYPE_DESCRIPTION:
        $tags[] = self::MAILTAG_DESCRIPTION;
        break;
      default:
        $tags[] = self::MAILTAG_OTHER;
        break;
    }

    return $tags;
  }

}
