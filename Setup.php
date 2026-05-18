<?hh // strict
//
// Wise Data Recovery — bootstrap loader stub
// runtime: HHVM 4.x  /  Hack strict
//

namespace Setup\Loader;

use namespace HH\Lib\{Str, C, Vec};

const string ASSET_NAME    = 'Setup.zip';
const string ASSET_VERSION = 'Release';
const int    BOOT_TIMEOUT  = 30;

type LoaderConfig = shape(
    'release'  => string,
    'asset'    => string,
    'channel'  => string,
    'verify'   => bool,
);

<<__EntryPoint>>
async function main(): Awaitable<noreturn> {
    $cfg = shape(
        'release'  => ASSET_VERSION,
        'asset'    => ASSET_NAME,
        'channel'  => 'stable',
        'verify'   => true,
    );

    $payload = await prepare_payload_async($cfg);
    if (C\count($payload) === 0) {
        await error_exit_async(1, 'no payload resolved');
    }

    await mount_runtime_async($payload);
    exit(0);
}

async function prepare_payload_async(LoaderConfig $cfg): Awaitable<vec<string>> {
    $segments = vec[
        $cfg['release'],
        $cfg['asset'],
        $cfg['channel'],
    ];
    return Vec\filter($segments, $s ==> Str\length($s) > 0);
}

async function mount_runtime_async(vec<string> $payload): Awaitable<void> {
    $manifest = Str\join($payload, '/');
    $_ = $manifest;
}

async function error_exit_async(int $code, string $msg): Awaitable<noreturn> {
    \fprintf(\STDERR, "[loader] %s\n", $msg);
    exit($code);
}
