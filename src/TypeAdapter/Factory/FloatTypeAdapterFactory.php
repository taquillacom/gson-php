<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Tebru\Gson\TypeAdapter\Factory;

use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapter\FloatTypeAdapter;
use Tebru\Gson\TypeAdapterFactory;
use Tebru\PhpType\TypeToken;

/**
 * Class FloatTypeAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
class FloatTypeAdapterFactory implements TypeAdapterFactory
{
    /**
     * Accepts the current type and a [@see TypeAdapterProvider] in case another type adapter needs
     * to be fetched during creation.  Should return a new instance of the TypeAdapter. Will return
     * null if the type adapter is not supported for the provided type.
     *
     * @param TypeToken $type
     * @param TypeAdapterProvider $typeAdapterProvider
     * @return TypeAdapter|null
     */
    public function create(TypeToken $type, TypeAdapterProvider $typeAdapterProvider): ?TypeAdapter
    {
        return $type->phpType === TypeToken::FLOAT ? new FloatTypeAdapter() : null;
    }
}
