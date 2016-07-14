<?php

/* user/index.html.twig */
class __TwigTemplate_ff00a8f967fefeb4088cf426ec47b39377379647dbb7aa0f0273dcd859c2a31c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "user/index.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_body($context, array $blocks = array())
    {
        // line 3
        echo "    <a href=\"/users/new\">Add User</a>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th colspan=\"3\">Actions</th>
            </tr>
        </thead>
        <tbody>
        ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["users"]) ? $context["users"] : $this->getContext($context, "users")));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 14
            echo "            <tr>
                <td>";
            // line 15
            echo twig_escape_filter($this->env, $this->getAttribute($context["user"], "name", array()), "html", null, true);
            echo "</td>
                <td>";
            // line 16
            echo twig_escape_filter($this->env, $this->getAttribute($context["user"], "email", array()), "html", null, true);
            echo "</td>
                <td><a href=\"/users/";
            // line 17
            echo twig_escape_filter($this->env, $this->getAttribute($context["user"], "id", array()), "html", null, true);
            echo "/edit\">Update</a></td>
                <td>
                    <!--参考http://symfony.com/doc/current/cookbook/routing/method_parameters.html-->
                    <form action=\"/users/";
            // line 20
            echo twig_escape_filter($this->env, $this->getAttribute($context["user"], "id", array()), "html", null, true);
            echo "\" method=\"POST\">
                        <input type=\"hidden\" name=\"_method\" value=\"DELETE\" />
                        <input type=\"submit\" value=\"Delete\" onclick=\"return confirm('你真的确定要删除吗');\" />
                    </form>
                </td>
                <td><a href=\"/users/";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($context["user"], "id", array()), "html", null, true);
            echo "/user_profiles/\">Profile</a></td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 28
        echo "        </tbody>
    </table>
    total count: ";
        // line 30
        echo twig_escape_filter($this->env, twig_length_filter($this->env, (isset($context["users"]) ? $context["users"] : $this->getContext($context, "users"))), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "user/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 30,  81 => 28,  72 => 25,  64 => 20,  58 => 17,  54 => 16,  50 => 15,  47 => 14,  43 => 13,  31 => 3,  28 => 2,  11 => 1,);
    }
}
/* {% extends 'base.html.twig' %}*/
/* {% block body %}*/
/*     <a href="/users/new">Add User</a>*/
/*     <table>*/
/*         <thead>*/
/*             <tr>*/
/*                 <th>Name</th>*/
/*                 <th>Email</th>*/
/*                 <th colspan="3">Actions</th>*/
/*             </tr>*/
/*         </thead>*/
/*         <tbody>*/
/*         {% for user in users %}*/
/*             <tr>*/
/*                 <td>{{ user.name }}</td>*/
/*                 <td>{{ user.email }}</td>*/
/*                 <td><a href="/users/{{ user.id }}/edit">Update</a></td>*/
/*                 <td>*/
/*                     <!--参考http://symfony.com/doc/current/cookbook/routing/method_parameters.html-->*/
/*                     <form action="/users/{{ user.id }}" method="POST">*/
/*                         <input type="hidden" name="_method" value="DELETE" />*/
/*                         <input type="submit" value="Delete" onclick="return confirm('你真的确定要删除吗');" />*/
/*                     </form>*/
/*                 </td>*/
/*                 <td><a href="/users/{{ user.id }}/user_profiles/">Profile</a></td>*/
/*             </tr>*/
/*         {% endfor %}*/
/*         </tbody>*/
/*     </table>*/
/*     total count: {{ users|length }}*/
/* {% endblock %}*/
