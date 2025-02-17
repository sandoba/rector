<?php

declare (strict_types=1);
namespace Rector\TypeDeclaration\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Php\PhpVersionProvider;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\NodeTypeResolver\TypeComparator\TypeComparator;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\TypeDeclaration\ValueObject\AddParamTypeDeclaration;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use RectorPrefix202307\Webmozart\Assert\Assert;
/**
 * @see \Rector\Tests\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector\AddParamTypeDeclarationRectorTest
 */
final class AddParamTypeDeclarationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     * @var \Rector\NodeTypeResolver\TypeComparator\TypeComparator
     */
    private $typeComparator;
    /**
     * @readonly
     * @var \Rector\Core\Php\PhpVersionProvider
     */
    private $phpVersionProvider;
    /**
     * @var AddParamTypeDeclaration[]
     */
    private $addParamTypeDeclarations = [];
    /**
     * @var bool
     */
    private $hasChanged = \false;
    public function __construct(TypeComparator $typeComparator, PhpVersionProvider $phpVersionProvider)
    {
        $this->typeComparator = $typeComparator;
        $this->phpVersionProvider = $phpVersionProvider;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Add param types where needed', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function process($name)
    {
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function process(string $name)
    {
    }
}
CODE_SAMPLE
, [new AddParamTypeDeclaration('SomeClass', 'process', 0, new StringType())])]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Class_::class, Interface_::class];
    }
    /**
     * @param Class_|Interface_ $node
     */
    public function refactor(Node $node) : ?Node
    {
        foreach ($node->getMethods() as $classMethod) {
            if ($this->shouldSkip($node, $classMethod)) {
                continue;
            }
            foreach ($this->addParamTypeDeclarations as $addParamTypeDeclaration) {
                if (!$this->isName($classMethod, $addParamTypeDeclaration->getMethodName())) {
                    continue;
                }
                if (!$this->isObjectType($node, $addParamTypeDeclaration->getObjectType())) {
                    continue;
                }
                $this->refactorClassMethodWithTypehintByParameterPosition($classMethod, $addParamTypeDeclaration);
            }
            if (!$this->hasChanged) {
                return null;
            }
        }
        return $node;
    }
    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration) : void
    {
        Assert::allIsAOf($configuration, AddParamTypeDeclaration::class);
        $this->addParamTypeDeclarations = $configuration;
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Interface_ $classLike
     */
    private function shouldSkip($classLike, ClassMethod $classMethod) : bool
    {
        // skip class methods without args
        if ($classMethod->params === []) {
            return \true;
        }
        // skip class without parents/interfaces
        if ($classLike instanceof Class_ && $classLike->implements !== []) {
            return \false;
        }
        return !$classLike->extends instanceof Name;
    }
    private function refactorClassMethodWithTypehintByParameterPosition(ClassMethod $classMethod, AddParamTypeDeclaration $addParamTypeDeclaration) : void
    {
        $parameter = $classMethod->params[$addParamTypeDeclaration->getPosition()] ?? null;
        if (!$parameter instanceof Param) {
            return;
        }
        $this->refactorParameter($parameter, $addParamTypeDeclaration);
    }
    private function refactorParameter(Param $param, AddParamTypeDeclaration $addParamTypeDeclaration) : void
    {
        // already set → no change
        if ($param->type !== null) {
            $currentParamType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($param->type);
            if ($this->typeComparator->areTypesEqual($currentParamType, $addParamTypeDeclaration->getParamType())) {
                return;
            }
        }
        $paramTypeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode($addParamTypeDeclaration->getParamType(), TypeKind::PARAM);
        $this->hasChanged = \true;
        // remove it
        if ($addParamTypeDeclaration->getParamType() instanceof MixedType) {
            if ($this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::MIXED_TYPE)) {
                $param->type = $paramTypeNode;
                return;
            }
            $param->type = null;
            return;
        }
        $param->type = $paramTypeNode;
    }
}
