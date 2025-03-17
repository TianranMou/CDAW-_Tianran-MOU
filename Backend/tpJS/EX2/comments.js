"use strict";

// 修改评论的函数
function modify(e) {
    // 获取要修改的评论div和内容
    const commentDiv = e.currentTarget.parentNode;
    const userId = commentDiv.id;
    const commentText = commentDiv.querySelector("p").textContent;
    
    // 显示表单并填充当前评论内容
    const form = document.getElementById("myForm");
    form.style.display = "block";
    
    // 启用表单元素
    const textarea = document.getElementById("commentText");
    textarea.disabled = false;
    textarea.value = commentText;
    
    const submitButton = document.getElementById("submitComment");
    submitButton.disabled = false;
    
    // 保存正在编辑的用户ID
    document.getElementById("currentUserId").value = userId;
}

// 删除评论的函数
function deleter(e) {
    // 获取要删除的div（按钮的父元素）
    const commentDiv = e.currentTarget.parentNode;
    
    // 从DOM中移除该元素
    commentDiv.remove();
    
    // 显示确认信息
    alert("Comment deleted!");
}

// 表单提交处理
document.getElementById("myForm").addEventListener("submit", function(e) {
    e.preventDefault(); // 阻止默认提交行为
    
    const textarea = document.getElementById("commentText");
    const commentText = textarea.value.trim();
    
    // 验证评论内容
    if (commentText === "") {
        alert("Comment cannot be empty!");
        return;
    }
    
    // 获取当前编辑的用户ID
    const userId = document.getElementById("currentUserId").value;
    const userDiv = document.getElementById(userId);
    
    // 更新评论内容
    if (userDiv) {
        userDiv.querySelector("p").textContent = commentText;
        alert("Updated comment for " + userId + "!");
        
        // 隐藏并重置表单
        resetForm();
    }
});

// 重置表单函数
function resetForm() {
    const form = document.getElementById("myForm");
    form.style.display = "none";
    
    const textarea = document.getElementById("commentText");
    textarea.disabled = true;
    textarea.value = "";
    
    document.getElementById("submitComment").disabled = true;
    document.getElementById("currentUserId").value = "";
}

// 添加评论的功能
document.getElementById("addNew").addEventListener("click", function(e) {
    // 获取用户容器
    const usersContainer = document.getElementById("users");
    
    // 获取所有用户div
    const userDivs = usersContainer.querySelectorAll("div[id^='user']");
    let maxId = 0;
    
    // 找出最大ID号
    userDivs.forEach(div => {
        const idNumber = parseInt(div.id.replace("user", ""));
        if (idNumber > maxId) {
            maxId = idNumber;
        }
    });
    
    // 创建新的ID
    const newId = "user" + (maxId + 1);
    
    // 创建新的评论元素
    const newCommentDiv = document.createElement("div");
    newCommentDiv.id = newId;
    newCommentDiv.innerHTML = `
        <h4>New User</h4>
        <p>This is a new comment...</p>
        <button class="modify">Modify Comment</button>
        <button class="remove">Remove Comment</button>
    `;
    
    // 添加到DOM
    usersContainer.appendChild(newCommentDiv);
    
    // 为新添加的按钮添加事件监听器
    newCommentDiv.querySelector(".modify").addEventListener("click", modify);
    newCommentDiv.querySelector(".remove").addEventListener("click", deleter);
    
    alert("New comment added!");
});

// 初始化：为所有修改按钮添加事件监听器
let modifiers = document.getElementsByClassName("modify");
Array.from(modifiers).forEach(m => m.addEventListener("click", modify));

// 初始化：为所有删除按钮添加事件监听器
let remover = document.getElementsByClassName("remove");
Array.from(remover).forEach(m => m.addEventListener("click", deleter));