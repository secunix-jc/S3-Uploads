<?php

class S3_Uploads_Stream_Wrapper extends Aws\S3\StreamWrapper {

	/**
	 * Register the 's3://' stream wrapper
	 *
	 * @param S3Client $client Client to use with the stream wrapper
	 */
	public static function register( Aws\S3\S3Client $client)
	{
		if (in_array('s3', stream_get_wrappers())) {
			stream_wrapper_unregister('s3');
		}

		stream_wrapper_register('s3', __CLASS__, STREAM_IS_URL);
		static::$client = $client;
	}

	// Override
	public function stream_flush() {

		// Add some HTTP headers which can be defined in wp-config.php

		// Expires:
		if ( defined( 'S3_UPLOADS_EXPIRES' ) ) {
			$this->params[ 'Expires' ] = S3_UPLOADS_EXPIRES;
		}

		// Cache-Control:
		if ( defined( 'S3_UPLOADS_CACHE_CONTROL' ) ) {
			if ( is_numeric( S3_UPLOADS_CACHE_CONTROL ) ) {
				$this->params[ 'CacheControl' ] = 'max-age='. S3_UPLOADS_CACHE_CONTROL;
			} else {
				$this->params[ 'CacheControl' ] = S3_UPLOADS_CACHE_CONTROL;
			}
		}

		// Theses are the parameters passed to S3Client::putObject()
		// http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_putObject
		$this->params = apply_filters( 's3_uploads_putObject_params', $this->params );

		parent::stream_flush();

		return;
	}

	public function stream_metadata( $path, $option, $value ) {
		// not implemented
	}
}
