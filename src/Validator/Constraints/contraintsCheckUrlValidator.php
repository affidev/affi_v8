<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Service\CurlUrl;


class contraintsCheckUrlValidator extends ConstraintValidator {

    private $curl;

    /**
     * contraintsCheckUrlValidator constructor.
     * @param CurlUrl $curl
     */
    public function __construct(CurlUrl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->curl->findUrl($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

}
