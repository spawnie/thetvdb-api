<?php namespace Choi\TheTvDbApi;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Series {

	/**
	 * Config object
	 *
	 * @var Choi\TheTvDbApi\Config
	 */
	private $config;

	/**
	 * Series ID
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Series data array
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Banners collection
	 *
	 * @var Illuminate\Support\Collection
	 */
	private $banners;

	/**
	 * Constructor
	 *
	 * @param  Choi\TheTvDbApi\Config $config
	 * @param  int $id
	 * @return void
	 */
	public function __construct(Config $config, $id)
	{
		$this->config = $config;
		$this->id     = $id;
	}

	/**
	 * Magic getter
	 *
	 * @param  string $key
	 * @return mixed|null
	 */
	public function __get($key)
	{
		return (isset($this->data[$key])) ? $this->data[$key] : null;
	}

	/**
	 * Series base details
	 *
	 * @param  void
	 * @return array
	 */
	public function getBaseDetails()
	{
		$client   = new Client;
		$response = $client->get(sprintf(
			'%s/api/%s/series/%s/%s.xml',
			$this->config->mirror,
			$this->config->api_key,
			$this->id,
			$this->config->language
		));
		
		return $this->data = $this->parseSeriesDetails($response->xml()->Series);
	}

	/**
	 * Series full details
	 *
	 * @param  void
	 * @return array
	 */
	public function getFullDetails()
	{
		$client   = new Client;
		$response = $client->get(sprintf(
			'%s/api/%s/series/%s/all/%s.xml',
			$this->config->mirror,
			$this->config->api_key,
			$this->id,
			$this->config->language
		));
		$body = $response->xml();

		$series = $this->parseSeriesDetails($body->Series);

		$seasons = [];
		foreach($body->Episode as $episode)
		{
			$data = $this->parseEpisodeDetails($episode);
			$seasons[$data['season_number']][$data['episode_number']] = $data;
		}

		return $this->data = compact('series', 'seasons');
	}

	public function getBanners()
	{
		if(is_null($this->banners))
		{
			$client   = new Client;
			$response = $client->get(sprintf(
				'%s/api/%s/series/%s/banners.xml',
				$this->config->mirror,
				$this->config->api_key,
				$this->id
			));
			$body = $response->xml();

			$banners = [];
			foreach($body->Banner as $banner)
			{
				$banners[] = new Banner($this->config, [
					'id'             => (int) $banner->id,
					'banner_path'    => (string) $banner->BannerPath,
					'banner_type'    => (string) $banner->BannerType,
					'banner_type2'   => (string) $banner->BannerType2,
					'colors'         => $this->pipeStringToArray($banner->Colors),
					'language'       => (string) $banner->Language,
					'season'         => (int) $banner->Season,
					'rating'         => (float) $banner->Rating,
					'rating_count'   => (int) $banner->RatingCount,
					'series_name'    => (bool) ($banner->SeriesName == 'true'),
					'thumbnail_path' => (string) $banner->ThumbnailPath,
					'vignette_path'  => (string) $banner->VignettePath,
					'full_path'      => (string) $this->config->mirror.'/banners/'.$banner->BannerPath,
				]);
			}
			
			$this->banners = new Collection($banners);
		}

		return $this->banners;
	}

	/**
	 * Parse Series XML data
	 *
	 * @param  SimpleXMLElement $data
	 * @return array
	 */
	private function parseSeriesDetails($data)
	{
		return [
			'id'             => (int) $data->id,
			'actors'         => $this->pipeStringToArray($data->Actors),
			'airs_dayofweek' => (string) $data->Airs_DayOfWeek,
			'airs_time'      => (string) $data->Airs_Time,
			'content_rating' => (string) $data->ContentRating,
			'first_aired'    => (string) $data->First_Aired,
			'genre'          => $this->pipeStringToArray($data->Genre),
			'imdb_id'        => (string) $data->IMDB_ID,
			'language'       => (string) $data->Language,
			'network'        => (string) $data->Network,
			'network_id'     => (string) $data->NetworkID,
			'overview'       => (string) $data->Overview,
			'rating'         => (float) $data->Rating,
			'rating_count'   => (int) $data->RatingCount,
			'runtime'        => (int) $data->Runtime,
			'series_id'      => (int) $data->SeriesID,
			'series_name'    => (string) $data->SeriesName,
			'status'         => (string) $data->Status,
			'added'          => (string) $data->added,
			'added_by'       => (string) $data->addedBy,
			'banner'         => (string) $data->banner,
			'fanart'         => (string) $data->fanart,
			'last_updated'   => (string) $data->lastupdated,
			'poster'         => (string) $data->poster,
			'zap2it_id'      => (string) $data->zap2it_id,
		];
	}

	/**
	 * Parse Episode XML data
	 *
	 * @param  SimpleXMLElement $data
	 * @return array
	 */
	private function parseEpisodeDetails($data)
	{
		return [
			'id'                      => (int) $data->id,
			'combined_episode_number' => (int) $data->Combined_episodenumber,
			'combined_season'         => (int) $data->Combined_season,
			'dvd_chapter'             => (string) $data->DVD_chapter,
			'dvd_disc_id'             => (string) $data->DVD_discid,
			'dvd_episode_number'      => (string) $data->DVD_episodenumber,
			'dvd_season'              => (string) $data->DVD_season,
			'director'                => (string) $data->Director,
			'ep_img_flag'             => (int) $data->EpImgFlag,
			'episode_name'            => (string) $data->EpisodeName,
			'episode_number'          => (int) $data->EpisodeNumber,
			'first_aired'             => (string) $data->FirstAired,
			'guest_stars'             => $this->pipeStringToArray($data->GuestStars),
			'imdb_id'                 => (string) $data->IMDB_ID,
			'language'                => (string) $data->Language,
			'overview'                => (string) $data->Overview,
			'production_code'         => (string) $data->ProductionCode,
			'rating'                  => (float) $data->Rating,
			'rating_count'            => (int) $data->RatingCount,
			'season_number'           => (int) $data->SeasonNumber,
			'writer'                  => $this->pipeStringToArray($data->Writer),
			'absolute_number'         => (string) $data->absolute_number,
			'airs_after_season'       => (int) $data->airsafter_season,
			'airs_before_episode'     => (string) $data->airsbefore_episode,
			'airs_before_season'      => (int) $data->airsbefore_season,
			'filename'                => (string) $data->filename,
			'last_updated'            => (string) $data->lastupdated,
			'season_id'               => (int) $data->seasonid,
			'series_id'               => (int) $data->seriesid,
			'thumb_added'             => (string) $data->thumb_added,
			'thumb_height'            => (int) $data->thumb_height,
			'thumb_width'             => (int) $data->thumb_width,
		];
	}

	/**
	 * Convert pipe concatenated string to array
	 *
	 * @param  string $data
	 * @return array
	 */
	private function pipeStringToArray($pipe_string)
	{
		return array_filter(explode('|', $pipe_string));
	}

}
