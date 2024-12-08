"use client";


import { zodResolver } from "@hookform/resolvers/zod";
import { ToastAction } from "@radix-ui/react-toast";
import { useRouter } from "next/navigation";
import { DefaultValues, FieldValues, Path, SubmitHandler, useForm } from "react-hook-form";
import { z, ZodType } from "zod";

import { createSession } from "../../lib/session";

import { Button } from "@/components/ui/button";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { useToast } from "@/hooks/use-toast";


interface AuthFormProps<T extends FieldValues> {
  schema: ZodType<T>;
  defaultValues: T;
}

const AuthForm = <T extends FieldValues>({ schema, defaultValues }: AuthFormProps<T>) => {
  const { toast } = useToast();
  const router = useRouter();
  const form = useForm<z.infer<typeof schema>>({
    resolver: zodResolver(schema),
    defaultValues: defaultValues as DefaultValues<T>,
  });

  const handleSubmit: SubmitHandler<T> = async () => {
    const myHeaders: Headers = new Headers();
    myHeaders.append("Content-Type", "application/json");

    const raw: string = JSON.stringify({
      username: form.getValues().username,
      password: form.getValues().username,
    });

    const requestOptions: RequestInit = {
      method: "POST",
      headers: myHeaders,
      body: raw,
      redirect: "follow",
    };

    await fetch("http://localhost/wp-json/jwt-auth/v1/token", requestOptions)
      .then((response) => response.json())
      .then(async (result) => {
        console.log(result);
        if (!result.token) {
          toast({
            variant: "destructive",
            title: "Invalid credentials",
            description: "Your username or password is incorrect.",
            action: <ToastAction altText="Try again">Try again</ToastAction>,
          });
        } else {
          await createSession(result)
            .then(() => {
              toast({
                title: "You are successfuly logged in",
                description: "Welcome.",
              });
            })
            .then(() => {
              router.push("/");
            });
        }
      })
      .catch((error) => console.error(error));
  };

  const buttonText = "Sign In";

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(handleSubmit)} className="mt-10 space-y-6">
        {Object.keys(defaultValues).map((field) => (
          <FormField
            key={field}
            control={form.control}
            name={field as Path<T>}
            render={({ field }) => (
              <FormItem className="flex w-full flex-col gap-2.5">
                <FormLabel className="paragraph-medium text-dark200_light900">
                  {field.name === "username" ? "Username" : field.name.charAt(0).toUpperCase() + field.name.slice(1)}
                </FormLabel>
                <FormControl>
                  <Input
                    required
                    type={field.name === "password" ? "password" : "text"}
                    {...field}
                    className="paragraph-regular background-light900_dark300 light-border-2 no-focus text-dark300_light700 min-h-12 rounded-1.5 border"
                  />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
        ))}

        <Button
          disabled={form.formState.isSubmitting}
          className="primary-gradient paragraph-medium min-h-12 w-full rounded-2 px-4 py-3 font-inter !text-light-900"
        >
          {form.formState.isSubmitting ? "Signing In..." : buttonText}
        </Button>
      </form>
    </Form>
  );
};

export default AuthForm;
