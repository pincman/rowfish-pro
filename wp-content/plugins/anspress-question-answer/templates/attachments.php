<?php
/**
 * Template used to display attachments of question and answer.
 *
 * @package AnsPress
 * @author Rhaul Arya <rah12@live.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icons = array(
	'image/jpeg'               => 'file-image-o',
	'image/png'                => 'file-image-o',
	'image/jpg'                => 'file-image-o',
	'image/gif'                => 'file-image-o',
	'application/msword'       => 'file-word-o',
	'application/vnd.ms-excel' => 'file-excel-o',
	'application/pdf'          => 'file-pdf-o',
);
?>

<div class="ap-attachments">
	<h3><?php esc_attr_e( 'Attachments', 'anspress-question-answer' ); ?></h3>
	<?php foreach ( ap_get_attach() as $attach_id ) : ?>
		<?php $media = get_post( $attach_id ); ?>
		<a class="ap-attachment" href="<?php echo esc_url( wp_get_attachment_url( $media->ID ) ); ?>" target="_blank" title="<?php esc_attr_e( 'Download file', 'anspress-question-answer' ); ?>">
			<?php $icon = isset( $icons[ $media->post_mime_type ] ) ? $icons[ $media->post_mime_type ] : 'file-archive-o'; ?>
			<i class="apicon-<?php echo esc_attr( $icon ); ?>"></i>
			<span><?php echo esc_html( basename( get_attached_file( $media->ID ) ) ); ?></span>
		</a>
	<?php endforeach; ?>
</div>
