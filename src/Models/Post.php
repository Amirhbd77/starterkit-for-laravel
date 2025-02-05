<?php

namespace Xmen\StarterKit\Models;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;
use Xmen\StarterKit\Helpers\TDate;

/**
 * App\News
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $subtitle
 * @property string $body
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post newQuery()
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\Post withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\Post withoutTrashed()
 * @mixin \Eloquent
 * @property int $user_id
 * @property int $is_breaking
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereIsBreaking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereUserId($value)
 * @property int $status
 * @property array $tag_names
 * @property-read \Illuminate\Database\Eloquent\Collection|\Tagged[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conner\Tagging\Model\Tagged[] $tagged
 * @property-read int|null $tagged_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post withAllTags($tagNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post withAnyTag($tagNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post withoutTags($tagNames)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Te7aHoudini\LaravelTrix\Models\TrixAttachment[] $trixAttachments
 * @property-read int|null $trix_attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Te7aHoudini\LaravelTrix\Models\TrixRichText[] $trixRichText
 * @property-read int|null $trix_rich_text_count
 * @property string $hash
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereHash($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Comment[] $approved_comments
 * @property-read int|null $approved_comments_count
 * @property int $is_pinned
 * @property int $like
 * @property int $dislike
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereDislike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\Post whereLike($value)
 */
class Post extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Taggable, HasTrixRichText, Searchable;


    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $t = explode('x',config('starter-kit.post_thumb'));

        if (config('starter-kit.post_thumb') == null || config('starter-kit.post_thumb') == ''){
            $t[0] = 500 ;
            $t[1] = 500 ;
        }


        $this->addMediaConversion('posts-image')
            ->width($t[0])
            ->height($t[1])
            ->crop(Manipulations::CROP_CENTER, $t[0], $t[1])
            ->optimize()
            ->sharpen(10);
    }

    public function imgurl()
    {
        if ($this->getMedia()->count() > 0) {
            return $this->getMedia()->first()->getUrl('posts-image');
        } else {
            return "no image";
        }
    }

    public function orgurl()
    {
        if ($this->getMedia()->count() > 0) {
            return $this->getMedia()[$this->image_index]->getUrl();
        } else {
            return asset('/images/logo.png');

        }
    }


    public function spendTime()
    {
        $word = strlen(strip_tags($this->body));
        $m = ceil($word / 1350);

        return $m . ' ' . __('minute');
    }

    public function persianDate()
    {
        $dt = TDate::GetInstance();

        return $dt->PDate("Y/m/d H:i:s", $this->created_at->timestamp);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approved_comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('status', 1);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body,
            'categories' => $this->categories->implode(' ') ?? null,
            'author' => $this->author->name ?? null,
            'tags' => $this->tags->implode(' ') ?? null,
        ];
    }
}
