<?php

/**
 * @var array $attributes Block Attributes
 * @var string $content Block Content
 * @var WP_Block $block Block
 */
if (empty($block->context)) {
    $block->context = $attributes;
}

$postID = $attributes['postId'] ?? $block->context['postId'] ?? get_the_ID();
?>

<?php
$format = $attributes['format'] ?? get_option('date_format');
$showTime = $attributes['showTime'] ?? true;
$showEndDate = $attributes['showEndDate'] ?? true;
$oneLineRange = $attributes['oneLineRange'] ?? false;
$dateRangeSeparator = isset($attributes['dateRangeSeparator']) ? trim((string) $attributes['dateRangeSeparator']) : '';
if ($dateRangeSeparator === '') {
    $dateRangeSeparator = html_entity_decode('&#8211;', ENT_QUOTES, 'UTF-8');
}
$timeFormat = $attributes['timeFormat'] ?? 'H:i';
$timeSeparator = $attributes['timeSeparator'] ?? ' | ';
$event = getEventInfo($postID);

if (!$event['start']) {
    return;
}

$isMultiDay = eventIsMultiDay($postID);
$showTime = $showTime && !eventIsAllDay($postID);

$startDateOutput = date_i18n($format, $event['start']);
$endDateOutput = $event['end'] ? date_i18n($format, $event['end']) : '';

$startTimeOutput = $showTime ? date_i18n($timeFormat, $event['start']) : '';
$endTimeOutput = '';
if ($showTime && $event['end']) {
    $endTimeOutput = date_i18n($timeFormat, $event['end']);
}
?>
<div <?= get_block_wrapper_attributes(); ?>>
    <time datetime="<?= esc_attr(date_i18n('c', $event['start'])) ?>">
        <?php if ($isMultiDay && $event['end'] && $showEndDate) : ?>
            <?= esc_html($startDateOutput) ?>
            <?php if ($startTimeOutput) : ?>
                <?php if (preg_match('/<[^>]*>/', $timeSeparator)) : ?>
                    <?= wp_kses_post($timeSeparator) ?>
                <?php else : ?>
                    <?= esc_html($timeSeparator) ?>
                <?php endif; ?>
                <?= esc_html($startTimeOutput) ?>
            <?php endif; ?>
            <?php if ($oneLineRange) : ?>
                <?= esc_html($dateRangeSeparator) ?>
            <?php else : ?>
                <?= esc_html_x('to', 'Line break between start and end in multi-day range (lowercase)', 'custom-post-type-events'); ?><br>
            <?php endif; ?>
            <?= esc_html($endDateOutput) ?>
            <?php if ($endTimeOutput) : ?>
                <?php if (preg_match('/<[^>]*>/', $timeSeparator)) : ?>
                    <?= wp_kses_post($timeSeparator) ?>
                <?php else : ?>
                    <?= esc_html($timeSeparator) ?>
                <?php endif; ?>
                <?= esc_html($endTimeOutput) ?>
            <?php endif; ?>
        <?php else : ?>
            <?= esc_html($startDateOutput) ?>
            <?php if ($startTimeOutput) : ?>
                <?php if (preg_match('/<[^>]*>/', $timeSeparator)) : ?>
                    <?= wp_kses_post($timeSeparator) ?>
                <?php else : ?>
                    <?= esc_html($timeSeparator) ?>
                <?php endif; ?>
                <?= esc_html($startTimeOutput) ?>
                <?php if ($endTimeOutput) : ?>
                    <?= esc_html($dateRangeSeparator) ?><?= esc_html($endTimeOutput) ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </time>
</div>
