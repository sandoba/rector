<?php

declare (strict_types=1);
namespace Rector\PHPStanStaticTypeMapper\TypeMapper;

use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;
use Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface;
use Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind;
/**
 * @implements TypeMapperInterface<NeverType>
 */
final class NeverTypeMapper implements \Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface
{
    /**
     * @return class-string<Type>
     */
    public function getNodeClass() : string
    {
        return \PHPStan\Type\NeverType::class;
    }
    /**
     * @param NeverType $type
     */
    public function mapToPHPStanPhpDocTypeNode(\PHPStan\Type\Type $type, \Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind $typeKind) : \PHPStan\PhpDocParser\Ast\Type\TypeNode
    {
        if ($typeKind->equals(\Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind::RETURN())) {
            return new \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode('never');
        }
        return new \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode('mixed');
    }
    /**
     * @param NeverType $type
     */
    public function mapToPhpParserNode(\PHPStan\Type\Type $type, \Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind $typeKind) : ?\PhpParser\Node
    {
        return null;
    }
}
