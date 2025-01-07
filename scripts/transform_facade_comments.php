<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configs = [
    \WebmanTech\LaravelFilesystem\Facades\Storage::class => [
        'from' => \Illuminate\Support\Facades\Storage::class,
        'change' => [
            'return' => [
                '\Illuminate\Contracts\Filesystem\Filesystem' => '\Illuminate\Filesystem\FilesystemAdapter',
                '\Illuminate\Contracts\Filesystem\Cloud' => '\Illuminate\Filesystem\FilesystemAdapter',
                '\Symfony\Component\HttpFoundation\StreamedResponse' => '\Webman\Http\Response',
            ],
        ],
        'add' => [
            ' *',
            ' * @see \Illuminate\Support\Facades\Storage',
            ' * @see \Illuminate\Filesystem\FilesystemAdapter',
        ],
    ],
    \WebmanTech\LaravelFilesystem\Facades\File::class => [
        'from' => \Illuminate\Support\Facades\File::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Filesystem\Filesystem',
            ' * @see \Illuminate\Support\Facades\File',
        ]
    ],
];

foreach ($configs as $targetClass => $config) {
    $methods = read_class_static_methods_from_comments($config['from']);

    $comments = [
        '/**'
    ];
    foreach ($methods as $info) {
        if (isset($config['change']['return'])) {
            $value = $info['return'];
            $info['return'] = $config['change']['return'][$value] ?? $value;
        }
        $comments[] = " * @method static {$info['return']} {$info['function_full']}";
    }

    $comments = array_merge($comments, $config['add'] ?? []);
    $comments[] = ' */';

    write_comments_to_class($targetClass, implode("\n", $comments));
    echo "Write comments to $targetClass\n";
}

function read_class_static_methods_from_comments(string $className): array
{
    $reflection = new ReflectionClass($className);
    $comments = $reflection->getDocComment();
    return collect(explode("\n", $comments))
        ->map(function ($comment) {
            $comment = trim($comment, "/* \t\n\r");
            if ($comment && str_starts_with($comment, '@method static')) {
                $comment = str_replace('@method static ', '', $comment);
                $return = explode(' ', $comment)[0];
                $functionFull = trim(str_replace($return, '', $comment));
                $function = explode('(', $functionFull)[0];
                return [
                    'return' => $return,
                    'function' => $function,
                    'function_full' => $functionFull,
                ];
            }
            return $comment;
        })
        ->filter(fn($comment) => is_array($comment))
        ->toArray();
}

function write_comments_to_class(string $className, string $comments): void
{
    $reflection = new ReflectionClass($className);
    $file = $reflection->getFileName();
    $oldComments = $reflection->getDocComment();
    $content = file_get_contents($file);
    $content = str_replace($oldComments, $comments, $content);
    file_put_contents($file, $content);
}
