<h2 class="mb-3">
  <i class="fas fa-pen">質問を投稿する</i>
</h2>
<?= $this->Form->create($question)?>
<?= $this->Form->control('body', ['type' => 'textarea', 'label' => false, 'maxlength' => 200])?>
<?= $this->Form->button('投稿する', ['class' => 'btn btn-warning']) ?>
<?= $this->Form->end()?>
