<?php

declare (strict_types=1);
namespace Rector\PHPStanStaticTypeMapper\TypeMapper;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\Type;
use Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface;
use Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind;
use Rector\StaticTypeMapper\ValueObject\Type\ParentStaticType;
/**
 * @implements TypeMapperInterface<ParentStaticType>
 */
final class ParentStaticTypeMapper implements \Rector\PHPStanStaticTypeMapper\Contract\TypeMapperInterface
{
    /**
     * @return class-string<Type>
     */
    public function getNodeClass() : string
    {
        return \Rector\StaticTypeMapper\ValueObject\Type\ParentStaticType::class;
    }
    /**
     * @param ParentStaticType $type
     */
    public function mapToPHPStanPhpDocTypeNode(\PHPStan\Type\Type $type, \Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind $typeKind) : \PHPStan\PhpDocParser\Ast\Type\TypeNode
    {
        return new \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode('parent');
    }
    /**
     * @param ParentStaticType $type
     */
    public function mapToPhpParserNode(\PHPStan\Type\Type $type, \Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind $typeKind) : ?\PhpParser\Node
    {
        return new \PhpParser\Node\Name('parent');
    }
}
