<?php

namespace Sebastienheyd\BoilerplateMediaManager\Tests\Components;

class FileTest extends TestComponent
{
    public function testFileComponentNoName()
    {
        $expected = <<<'HTML'
<code>&lt;x-boilerplate-media-manager::file> The name attribute has not been set</code>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->blade('<x-boilerplate-media-manager::file />');
            $this->assertEquals($expected, $view);
        }

        $view = $this->blade("@component('boilerplate-media-manager::file') @endcomponent");
        $this->assertEquals($expected, $view);
    }

    public function testFileComponentName()
    {
        $expected = <<<'HTML'
<div class="form-group">
        <div class="input-group">
        <div class="input-group-prepend">
            <button type="button" class="btn-select-file btn btn-secondary" data-field="test" data-src="/admin/medias?mce=1&type=all&return_type=file&field=test">
                <i class="far fa-folder-open"></i>
            </button>
        </div>
        <input type="text" class="form-control" data-id="text-test" value="" placeholder="No file selected" style="background: transparent" disabled>
        <input type="hidden" name="file" value="" data-id="test" data-action="setMediaFile"/>
        <button class="btn d-none" id="clear-test" type="button" data-action="clearMediaFile" style="position:absolute;right:0"><span class="fa fa-times"></span></button>
    </div>
</div>
<script>loadStylesheet("",()=>{loadScript("",()=>{window.selectMediaLocales={confirm:"Remove image ?"}})});</script>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->blade('<x-boilerplate-media-manager::file name="file" id="test"/>');
            $this->assertEquals($expected, $view);
        }

        $view = $this->blade("@component('boilerplate-media-manager::file', ['name' => 'file', 'id' => 'test']) @endcomponent");
        $this->assertEquals($expected, $view);
    }

    public function testFileComponentAll()
    {
        $expected = <<<'HTML'
<div class="form-group">
        <label for="file">label</label>
        <div class="input-group">
        <div class="input-group-prepend">
            <button type="button" class="btn-select-file btn btn-secondary" data-field="test" data-src="/admin/medias?mce=1&type=all&return_type=file&field=test">
                <i class="far fa-folder-open"></i>
            </button>
        </div>
        <input type="text" class="form-control" data-id="text-test" value="file.pdf" placeholder="No file selected" style="background: transparent" disabled>
        <input type="hidden" name="file" value="file.pdf" data-id="test" data-action="setMediaFile"/>
        <button class="btn " id="clear-test" type="button" data-action="clearMediaFile" style="position:absolute;right:0"><span class="fa fa-times"></span></button>
    </div>
</div>
<script>loadStylesheet("",()=>{loadScript("",()=>{window.selectMediaLocales={confirm:"Remove image ?"}})});</script>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->blade('<x-boilerplate-media-manager::file name="file" id="test" label="label" value="file.pdf"/>');
            $this->assertEquals($expected, $view);
        }

        $view = $this->blade("@component('boilerplate-media-manager::file', ['name' => 'file', 'id' => 'test', 'label' => 'label', 'value' => 'file.pdf']) @endcomponent");
        $this->assertEquals($expected, $view);
    }
}
