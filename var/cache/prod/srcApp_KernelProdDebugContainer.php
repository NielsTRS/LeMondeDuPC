<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerLQx5psI\srcApp_KernelProdDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerLQx5psI/srcApp_KernelProdDebugContainer.php') {
    touch(__DIR__.'/ContainerLQx5psI.legacy');

    return;
}

if (!\class_exists(srcApp_KernelProdDebugContainer::class, false)) {
    \class_alias(\ContainerLQx5psI\srcApp_KernelProdDebugContainer::class, srcApp_KernelProdDebugContainer::class, false);
}

return new \ContainerLQx5psI\srcApp_KernelProdDebugContainer(array(
    'container.build_hash' => 'LQx5psI',
    'container.build_id' => 'b2257c4e',
    'container.build_time' => 1548791926,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerLQx5psI');
