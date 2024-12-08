"use client";
import AuthForm from "@/components/forms/AuthForm";
import { SignInSchema } from "@/lib/validations";
const SignIn = () => {
  return <AuthForm schema={SignInSchema} defaultValues={{ username: "", password: "" }} />;
};

export default SignIn;
