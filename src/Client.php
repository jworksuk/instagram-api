<?php

namespace JWorksUK\InstagramApi;

use Exception;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use JWorksUK\InstagramApi\Responses\CommentsResponse;
use JWorksUK\InstagramApi\Responses\MediaResponse;
use JWorksUK\InstagramApi\Models\Comment;
use JWorksUK\InstagramApi\Models\Story;
use JWorksUK\InstagramApi\Models\Media;
use JWorksUK\InstagramApi\Models\Profile;
use GuzzleHttp\Client as GuzzleHttp;

class Client
{
    const string USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36' .
    ' (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36';
    const string LOCALE = 'en-EN';

    const string API_BASE = 'https://www.instagram.com/graphql/query/';

    public function __construct(protected GuzzleHttp $httpClient, protected CookieJar $cookies)
    {
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getProfile(string $username): Profile
    {
        $response = $this->httpClient->request(
            'GET',
            'https://i.instagram.com/api/v1/users/web_profile_info/',
            [
                'query' => [
                    'username' => $username,
                ],
                'cookies' => $this->getCookies(),
                'headers' => array_merge(
                    [
                        'x-ig-app-id' => 936619743392459,
                    ],
                    $this->getDefaultHeaders()
                )
            ]
        );

        $response = json_decode((string) $response->getBody(), true);

        if (!isset($response['data']['user'])) {
            throw new Exception('Response error');
        }

        $user = $response['data']['user'];

        return new Profile(
            $user['id'],
            $user['username'],
            $user['full_name'],
            $user['biography'],
            $user['edge_followed_by']['count'],
            $user['edge_follow']['count'],
            $user['profile_pic_url'],
            $user['external_url'],
            0,
            $user['is_private'],
            $user['is_verified'],
            !($user['business_category_name'] === null),
            $user['profile_pic_url_hd'] ?? null,
        );
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getProfileById(int $instagramId): Profile
    {
        $response = $this->httpClient->request(
            'GET',
            self::API_BASE,
            [
                'query' =>  [
                    'doc_id' => '9539110062771438',
                    'variables' => json_encode([
                        'id' => $instagramId,
                        'render_surface' => 'PROFILE'
                    ]),
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders(),
            ]
        );

        $response = json_decode((string) $response->getBody(), true);

        $user = $response['data']['user'];
        return new Profile(
            $user['pk'],
            $user['username'],
            $user['full_name'],
            $user['biography'],
            $user['follower_count'],
            $user['following_count'],
            $user['profile_pic_url'],
            $user['external_url'],
            0,
            $user['is_private'],
            $user['is_verified'],
            $user['is_business'],
            $user['hd_profile_pic_url_info']['url'] ?? null,
        );
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getStoriesByProfileId(int $profileId): Collection
    {
        $response = $this->httpClient->request(
            'GET',
            self::API_BASE,
            [
                'query' => [
                    'query_hash' => 'de8017ee0a7c9c45ec4260733d81ea31',
                    'variables' => json_encode([
                        "reel_ids" =>[$profileId],
                        "tag_names" =>[],
                        "location_ids" => [],
                        "highlight_reel_ids" => [],
                        "precomposed_overlay"=> false,
                        "show_story_viewer_list" => true,
                        "story_viewer_fetch_count" => 50,
                        "story_viewer_cursor"=> ""
                    ])
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders()
            ]
        );

        $response = json_decode((string) $response->getBody(), true);

        if (!isset($response['data']['reels_media'][0])) {
            return collect();
        }

        $reelsMedia = $response['data']['reels_media'][0];

        return collect($reelsMedia['items'])->map(function (array $story) {
            return Story::fromArray($story);
        });
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getMedia(string $username, ?string $endCursor = null, $limit = 12): MediaResponse
    {
        $response = $this->httpClient->request(
            'POST',
            self::API_BASE,
            [
                'form_params' => [
                    'doc_id' => '9066276850131169',
                    'variables' => json_encode([
                        'data' => [
                            'count' => $limit,
                            'include_reel_media_seen_timestamp' => true,
                            'include_relationship_info' => true,
                            'latest_besties_reel_media' => true,
                            'latest_reel_media' => true,
                        ],
                        'username' => $username,
                        '__relay_internal__pv__PolarisIsLoggedInrelayprovider' => true,
                        '__relay_internal__pv__PolarisShareSheetV3relayprovider' => false,
                        'after' => $endCursor
                    ]),
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders(),
            ]
        );

        $response = json_decode($response->getBody(), true);

        if (!isset($response['data']['xdt_api__v1__feed__user_timeline_graphql_connection']['edges'])) {
            throw new Exception('media not found');
        }

        $media = collect($response['data']['xdt_api__v1__feed__user_timeline_graphql_connection']['edges'])
            ->pluck('node')
            ->map(function ($post) {
                return Media::create($post);
            });

        $endCursor = $this->getEndCursor(
            $response['data']['xdt_api__v1__feed__user_timeline_graphql_connection']['page_info']
        );

        return new MediaResponse($media, $endCursor);
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getMediaDetail(string $mediaLink): Media
    {
        $response = $this->httpClient->request(
            'GET',
            $mediaLink,
            [
                'query' => [
                    '__a' => '1',
                    '__d' => 'dis'
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders()
            ]
        );

        $response = json_decode($response->getBody(), true);

        if (!isset($response['items'][0])) {
            throw new Exception('media not found');
        }

        return Media::create($response['items'][0]);
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getMediaByLocationId(
        int $locationId,
        ?string $endCursor = null,
        $limit = 12,
        string $tab = 'recent'
    ): MediaResponse {
        $response = $this->httpClient->request(
            'GET',
            self::API_BASE,
            [
                'query' => [
                    'doc_id' => '27699671799676675',
                    'variables' => json_encode([
                        'location_id' => $locationId,
                        'page_size_override' => $limit,
                        'tab' => $tab,
                        'after' => $endCursor
                    ]),
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders(),
            ]
        );

        $response = json_decode($response->getBody(), true);

        if (!isset($response['data']['xdt_location_get_web_info_tab']['edges'])) {
            throw new Exception('media not found');
        }

        $media = collect($response['data']['xdt_location_get_web_info_tab']['edges'])
            ->pluck('node')
            ->map(function ($post) {
                return Media::create($post);
            });

        $endCursor = $this->getEndCursor($response['data']['xdt_location_get_web_info_tab']['page_info']);

        return new MediaResponse($media, $endCursor);
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getCommentsByShortCode(
        string $shortCode,
        ?string $endCursor = null,
        int $limit = 50
    ): CommentsResponse {
        $response = $this->httpClient->request(
            'GET',
            self::API_BASE,
            [
                'query' => [
                    'query_hash' => '33ba35852cb50da46f5b5e889df7d159',
                    'variables' => json_encode([
                        'shortcode' => $shortCode,
                        'first' => $limit,
                        'after' => $endCursor
                    ]),
                ],
                'cookies' => $this->getCookies(),
                'headers' => $this->getDefaultHeaders(),
            ]
        );

        $response = json_decode($response->getBody(), true);

        if (!isset($response['data']['shortcode_media']['edge_media_to_comment']['edges'])) {
            throw new Exception('comments not found');
        }

        $media = collect($response['data']['shortcode_media']['edge_media_to_comment']['edges'])
            ->pluck('node')
            ->map(function ($post) {
                return Comment::create($post);
            });

        $endCursor = $this->getEndCursor($response['data']['shortcode_media']['edge_media_to_comment']['page_info']);

        return new CommentsResponse($media, $endCursor);
    }

    protected function getEndCursor(array $pageInfo): ?string
    {
        if ($pageInfo['has_next_page']) {
            return $pageInfo['end_cursor'];
        }
        return null;
    }

    protected function getDefaultHeaders(): array
    {
        return [
            'user-agent'       => static::USER_AGENT,
            'accept-language'  => static::LOCALE,
            'x-requested-with' => 'XMLHttpRequest',
        ];
    }

    public function getCookies(): CookieJar
    {
        return $this->cookies;
    }

    public function setCookies(CookieJar $cookies): void
    {
        $this->cookies = $cookies;
    }
}
