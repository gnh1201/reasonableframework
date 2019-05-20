<!doctype html>
<html>
<head>
    <title>Write to Wordpress</title>
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
</head>

<body>
    <div id="wrap">
        <form method="post" action="<?php echo base_url(); ?>" class="pure-form pure-form-aligned">
                <fieldset>
                        <legend>Write to Wordpress</legend>

                        <div class="hidden">
                            <input type="hidden" name="route" value="<?php echo $route; ?>">
                            <input type="hidden" name="action" value="write">
                            <input type="hidden" name="_token" value="<?php echo $_token; ?>">
                        </div>
                        <div class="pure-control-group">
                                <label for="categories">카테고리</label>
                                <select id="categories" name="categories">
                                    <option value="">선택하세요</option>
<?php
                                    foreach($categories as $category) {
?>
                                    <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
<?php
                                    }
?>
                                </select>
                        </div>

                        <div class="pure-control-group">
                                <label for="title">제목</label>
                                <input id="title" type="text" name="title" placeholder="제목">
                        </div>

                        <div class="pure-control-group">
                                <label for="content">내용</label>
                                <textarea id="content" name="content" placeholder="내용"></textarea>
                        </div>

                        <div class="pure-control-group">
                                <label for="status">공개유형</label>
                                <select id="status" name="status">
                                    <option value="publish">publish</option>
                                    <option value="draft">draft</option>
                                    <option value="future">future</option>
                                    <option value="pending">pending</option>
                                    <option value="pending">private</option>
                                </select>
                        </div>

                        <div class="pure-controls">
                                <button type="submit" class="pure-button pure-button-primary">등록</button>
                        </div>
                </fieldset>
        </form>
    </div>
</body>

</html>
