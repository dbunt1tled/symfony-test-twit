<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.12.18
 * Time: 12:07
 */

namespace App\Form\Api\Http\Requests\Posts;


use App\Form\Api\DTO\PostDTOInterface;
use App\Form\Api\RequestDTOInterface;
use App\Helpers\HttpHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class ManagePostsRequest implements RequestDTOInterface, PostDTOInterface
{
    /**
     * @var string
     * @NotBlank
     */
    private $category;
    /**
     * @var string
     * @NotBlank
     * @Length(min="6")
     */
    private $slug;
    /**
     * @var string
     * @NotBlank
     * @Length(min="6")
     */
    private $text;
    /**
     * @var string
     * @NotBlank
     * @Length(min="6")
     */
    private $title;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * ManagePostsRequest constructor.
     * @param Request $data
     */
    public function __construct(Request $data = null)
    {
        if ($data instanceof Request) {
            try {
                $data = HttpHelper::getContentAsArray($data);
            }catch (\Exception $exception) {
                $data = null;
            }
        }
        if (is_iterable($data) && count($data) > 0) {
            foreach ($data as $field => $value) {
                if (property_exists(static::class, $field)) {
                    $this->{$field} = $value;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     * @return $this
     */
    public function setErrors($errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return ManagePostsRequest
     */
    public function setCategory(string $category): ManagePostsRequest
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return ManagePostsRequest
     */
    public function setSlug(string $slug): ManagePostsRequest
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return ManagePostsRequest
     */
    public function setText(string $text): ManagePostsRequest
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ManagePostsRequest
     */
    public function setTitle(string $title): ManagePostsRequest
    {
        $this->title = $title;
        return $this;
    }



    // Alternate Method
    /*
    public function validateRequest(Request $request)
    {
        $validator = Validation::createValidator();

        $constraint = new Collection([
            'post[title]' => [
                new NotBlank(),
                new Length(['min'=> 10,]),
            ],
            'post[text]' => [
                new NotBlank(),
                new Length(['min'=> 10,]),
            ],
        ]);
        $violations = $validator->validate($request->request->all(), $constraint);
        return $violations;
    }
    /**/
}