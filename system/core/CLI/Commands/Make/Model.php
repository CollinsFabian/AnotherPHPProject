<?php

namespace Ziro\System\CLI\Commands\Make;

use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;

class Model extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $name = $args[0] ?? null;

        if (!$name) {
            $this::errorM('Model name required');
            return;
        }

        $name = str_replace(['/', '\\'], '', $name);
        $path = base_path('app/Entity');
        $filePath = $path . DIRECTORY_SEPARATOR . $name . '.php';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (file_exists($filePath)) {
            $this->errorM('Model already exists');
            return;
        }

        $template = <<<'PHP'
<?php

namespace Ziro\Entity;

class %s extends Model
{
    protected const TABLE = '%s';
}
PHP;

        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . 's';
        file_put_contents($filePath, sprintf($template, $name, $table));

        $this->successM("Model created: {$name}");
    }
}
