<?php

namespace Sebastienheyd\BoilerplateMediaManager\Tests\Components;

class ImageTest extends TestComponent
{
    public function testImageComponentNoName()
    {
        $expected = <<<'HTML'
<code>&lt;x-boilerplate-media-manager::image> The name attribute has not been set</code>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->renderBlade('<x-boilerplate-media-manager::image />');
            $this->assertEquals($expected, $view);
        }

        $view = $this->renderBlade("@component('boilerplate-media-manager::image') @endcomponent");
        $this->assertEquals($expected, $view);
    }

    public function testFileComponentName()
    {
        $expected = <<<'HTML'
<div class="form-group">
<div class="select-image-wrapper " style="width:300px;height:200px">
    <button type="button" style="max-width:300px;height:200px" class="btn-select-image" data-field="test" data-src="/admin/medias?mce=1&type=image&return_type=image&field=test">
        <span class="fa fa-image fa-3x"></span>
    </button>
    <div class="select-image-menu">
        <button class="btn select-image-view"><span class="fa fa-eye"></span></button>
        <button class="btn select-image-delete"><span class="fa fa-times"></span></button>
    </div>
    <input type="hidden" name="file" value="" data-id="test" data-name="hidden-image-selector-value"/>
</div>
</div>
<script>loadStylesheet("",()=>{loadScript("",()=>{window.selectMediaLocales={confirm:"Remove image ?"}})});</script>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->renderBlade('<x-boilerplate-media-manager::image name="file" id="test"/>');

            $this->assertEquals($expected, $view);
        }

        $view = $this->renderBlade("@component('boilerplate-media-manager::image', ['name' => 'file', 'id' => 'test']) @endcomponent");
        $this->assertEquals($expected, $view);
    }

    public function testFileComponentAll()
    {
        $expected = <<<'HTML'
<div class="form-group">
    <label for="image">label</label>
<div class="select-image-wrapper editable" style="width:300px;height:200px">
    <button type="button" style="max-width:300px;height:200px" class="btn-select-image" data-field="test" data-src="/admin/medias?mce=1&type=image&return_type=image&field=test">
        <img src="image.jpg" />
    </button>
    <div class="select-image-menu">
        <button class="btn select-image-view"><span class="fa fa-eye"></span></button>
        <button class="btn select-image-delete"><span class="fa fa-times"></span></button>
    </div>
    <input type="hidden" name="image" value="image.jpg" data-id="test" data-name="hidden-image-selector-value"/>
</div>
</div>
<script>loadStylesheet("",()=>{loadScript("",()=>{window.selectMediaLocales={confirm:"Remove image ?"}})});</script>
HTML;

        if ($this->isLaravelEqualOrGreaterThan7) {
            $view = $this->renderBlade('<x-boilerplate-media-manager::image name="image" id="test" label="label" value="image.jpg"/>');
            $this->assertEquals($expected, $view);
        }

        $view = $this->renderBlade("@component('boilerplate-media-manager::image', ['name' => 'image', 'id' => 'test', 'label' => 'label', 'value' => 'image.jpg']) @endcomponent");
        $this->assertEquals($expected, $view);
    }
}
